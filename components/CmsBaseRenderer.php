<?php
/**
 * CmsBaseRenderer class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

/**
 * Cms renderer base class. All renderers must be extended from this class.
 */
class CmsBaseRenderer extends CComponent
{
	protected $_patterns = array(
		'file'=>'/{{file:([\d]+)}}/i',
		'image'=>'/{{image:([\d]+)}}/i',
		'link'=>'/{{(#?[\w\d\._-]+|https?:\/\/[\w\d_-]*(\.[\w\d_-]*)+.*)\|([\w\d\s-]+)}}/i',
		'email'=>'/{{email:([\w\d!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[\w\d!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[\w\d](?:[\w\d-]*[\w\d])?\.)+[\w\d](?:[\w\d-]*[\w\d])?)}}/i',
		'node'=>'/{{node:([\w\d\._-]+)}}/i',
	);

	/**
	 * Renders a specific node.
	 * @param CmsNode $node the node to render
	 * @return string the rendered node
	 */
	public function render($node)
	{
		$heading = str_replace('{heading}', $node->heading, Yii::app()->cms->headingTemplate);
		$content = $this->renderHeading($heading, $node->body);
		$content = $this->renderLinks($content);
		$content = $this->renderEmails($content);
		$content = $this->renderImages($content);
		$content = $this->renderAttachments($content);
		$content = $this->renderNodes($content);

		return $content;
	}

	/**
	 * Renders a specific node as a widget.
	 * @param CmsNode $node the node to render
	 * @return string the rendered node
	 */
	public function renderWidget($node)
	{
		$heading = str_replace('{heading}', $node->heading, Yii::app()->cms->widgetHeadingTemplate);
		$content = $this->renderHeading($heading, $node->body);
		$content = $this->renderLinks($content);
		$content = $this->renderEmails($content);
		$content = $this->renderImages($content);
		$content = $this->renderAttachments($content);
		$content = $this->removeNodes($content); // widgets do not render inline nodes

		return $content;
	}

	/**
	 * Renders the heading.
	 * @param string $heading the heading to render
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderHeading($heading, $content)
	{
		return str_replace('{{heading}}', $heading, $content);
	}

	/**
	 * Renders nodes within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderNodes($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['node'], $content, $matches);

		$nodes = array();
		foreach ($matches[1] as $index => $name)
		{
			/** @var CmsNode $node */
			$node = Yii::app()->cms->loadNode($name);
			if ($node instanceof CmsNode)
				$nodes[$index] = $node->renderWidget();
		}

		if (!empty($nodes))
			$content = strtr($content, array_combine($matches[0], $nodes));

		return $content;
	}

	/**
	 * Renders links within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderLinks($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['link'], $content, $matches);

		$links = array();
		foreach ($matches[1] as $index => $target)
		{
			// If the target doesn't include 'http' it's treated as an internal link.
			if (strpos($target, '#') !== 0 && strpos($target, 'http') === false)
			{
				/** @var Cms $cms */
				$cms = Yii::app()->cms;

				/** @var CmsNode $node */
				$node = $cms->loadNode($target);
				if (!$node instanceof CmsNode)
				{
					$cms->createNode($target);
					$node = $cms->loadNode($target);
				}

				$target = $node->getUrl();
			}

			$text = $matches[3][$index];
			$links[$index] = CHtml::link($text, $target);
		}

		if (!empty($links))
			$content = strtr($content, array_combine($matches[0], $links));

		return $content;
	}

	/**
	 * Renders emails within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderEmails($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['email'], $content, $matches);

		$mails = array();
		foreach ($matches[1] as $index => $id)
		{
			$email = str_rot13($matches[1][$index]);
			$mails[$index] = CHtml::mailto($email, $email, array('class'=>'email', 'rel'=>'nofollow'));
		}

		if (!empty($mails))
		{
			$content = strtr($content, array_combine($matches[0], $mails));

			/** @var CClientScript $cs */
			$cs = Yii::app()->getClientScript();

			$assetsUrl = Yii::app()->cms->getAssetsUrl();
			$cs->registerScriptFile($assetsUrl.'/js/cms-rot13.js');
			$cs->registerScript(__CLASS__.'#'.uniqid(true, true).'_emailObfuscation', "
				(function($) {
					$('.email').each(function() {
						var element = $(this);

						if (!element.attr('data-decoded')) {
							var	href = $(this).attr('href'),
								address = Cms.Rot13.decode(href.substring(href.indexOf(':') + 1)),
								value = Cms.Rot13.decode($(this).text());

							element.attr('href', 'mailto:' + address);
							element.text(value);
							element.attr('data-decoded', 1);
						}
					});
				})(jQuery);
			");
		}

		return $content;
	}

	/**
	 * Renders images within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderImages($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['image'], $content, $matches);

		$images = array();
		foreach ($matches[1] as $index => $id)
		{
			/** @var CmsAttachment $attachment */
			$attachment = CmsAttachment::model()->findByPk($id);
			if ($attachment instanceof CmsAttachment && strpos($attachment->mimeType, 'image') !== false)
			{
				$url = $attachment->getUrl();
				$name = $attachment->resolveName();
				$images[$index] = CHtml::image($url, $name);
			}
		}

		if (!empty($images))
			$content = strtr($content, array_combine($matches[0], $images));

		return $content;
	}

	/**
	 * Renders attachments within this node.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	protected function renderAttachments($content)
	{
		$matches = array();
		preg_match_all($this->_patterns['file'], $content, $matches);

		$attachments = array();
		foreach ($matches[1] as $index => $id)
		{
			/** @var CmsAttachment $attachment */
			$attachment = CmsAttachment::model()->findByPk($id);
			if ($attachment instanceof CmsAttachment)
			{
				$url = $attachment->getUrl();
				$name = $attachment->resolveName();
				$attachments[$index] = CHtml::link($name, $url);
			}
		}

		if (!empty($attachments))
			$content = strtr($content, array_combine($matches[0], $attachments));

		return $content;
	}

	/**
	 * Removes the node tags within the given content.
	 * @param string $content the content being rendered
	 * @return string the content
	 */
	public function removeNodes($content)
	{
		return preg_replace($this->_patterns['node'], '', $content);
	}
}
