<div id="warp" class="wrap" data-warp="widget">
	
    <h2>Widget Options</h2>
	<p>Customize your widgets appearance and select your favorite style, icon or badge. To configure your widgets, please visit the <a href="widgets.php">widgets settings</a> screen.</p>

	<form method="post" action="">
		<ul class="widget-container">
		<?php
			$html = array();

			foreach ($position_settings as $name => $position) {
				if ($widgets = $this['widgets']->getWidgets($name)) {
					$html[] = sprintf('<li data-name="%s">', $name, $name);

	                foreach ($widgets as $widget) {

						$html[] = '<div class="widget-box postbox">';
						$html[] = '<h3>'.$widget->name.(isset($widget->params['title']) ? '<span class="small">: '.$widget->params['title'].'</span>' : null).'</h3>';
						$html[] = '<div class="content">';

						foreach ($module_settings as $node) {

						    $name  = $node->attr('name');
							$value = isset($widget->options[$name]) ? $widget->options[$name] : $node->attr('default');

							if (($settings = $position->attr('settings')) && !in_array($name, explode(' ', $settings))) {
								continue;
							}

					        $html[] = '<div class="option">';
	   				        $html[] = '<h4>'.$node->attr('label').'</h4>';
							$html[] = '<div class="value">'.$this['field']->render($node->attr('type'), 'warp_widget_options['.$widget->id.']['.$name.']', $value, $node, compact('widget')).'</div>';
					        $html[] = '</div>';
						}

						$html[] = '</div>';
						$html[] = '</div>';
					}

					$html[] = '</li>';
				}
			}

			echo implode("\n", $html);

			settings_fields('template-parameters');
		?>
		</ul>
		<input type="hidden" name="task" value="widget-options" />
		<input type="hidden" name="warp-ajax-save" value="1" />
		<p>
			<input type="submit" value="Save changes" class="button-primary"/><span></span>
		</p>
	</form>

</div>