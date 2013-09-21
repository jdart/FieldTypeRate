<?php

/**
 * ProcessWire Rate Fieldtype
 *
 * Fieldtype that gives a simple star rating on your website.
 *
 * by @jonathandart
 * 
 * ProcessWire 2.x 
 * Copyright (C) 2010 by Ryan Cramer 
 * Licensed under GNU/GPL v2, see LICENSE.TXT
 * 
 * http://www.processwire.com
 * http://www.ryancramer.com
 *
 */

Class RateHelper Extends WireData {

	protected $page = null;
	
	public function __construct($field, $page) {

		$this->name = $field->name;
		$this->default_score = $field->default_score;
		$this->star_count = $field->star_count;
		$this->page = $page;
		$this->thank_you_message = $field->thank_you_message;
	}

	public function render() {

		$value = $this->input->post->{$this->name};

		if ($value == $this->page->id) {
			$this->processVoting();
			return '<span class="thank-you-message">'.$this->thank_you_message.'</span>';
		}

		$id = $this->page->id . '-' . $this->name;

		ob_start();
?>
<div id="<?php echo $id ?>" class="star-rating">
	<form method='post' action='./'>
		<input name="<?php echo $this->name ?>" value="<?php echo $this->page->id ?>" type="hidden">
		<input name="score" value="" type="hidden">
	</form>
</div>
<?php
		$out = ob_get_clean();

		$root = $this->config->urls->siteModules;

		$alts = array('1 star');
		for ($i = 2; $i <= $this->star_count; $i++)
			$alts[] = sprintf('%d stars', $i);

		ob_start();
?>
<script type="text/javascript">
$('#<?php echo $id ?>').raty({
	number: <?php echo $this->star_count ?>,
	score: <?php echo $this->getScore() ?>,
	path: "<?php echo $root ?>FieldtypeRate/raty/lib/img",
	readOnly: <?php echo json_encode($this->userAlreadyVoted()) ?>,
	hints: <?php echo json_encode($alts) ?>,
	click: function(score, e) {
		var $form = $('#<?php echo $id ?> form');
		$form.find('[name=score]').val(score);
		$form.submit();
	}
});
</script>
<?php		
		$js = ob_get_clean();

		if (function_exists('docReady')) {
			addScript('FieldtypeRate/raty/lib/jquery.raty.min.js', $root);
			docReady($js);
		} else {
			$assets = "<script src='{$root}FieldtypeRate/raty/lib/jquery.raty.min.js' type='text/javascript'></script>";
			$out = $assets.$out.$js;
		}

		return $out;
	}

	public function getScore() {

		if ( ! $this->vote_count)
			return $this->default_score;

		return $this->vote_sum / $this->vote_count;
	}

	public function processVoting() {

		$userAgentAndIP = $this->getUaString();
		if (empty($this->vote_log))
			$this->vote_log = "\n";
		$this->vote_log .= $userAgentAndIP."\n";

		$this->addVote();

		$this->page->of(false);
		$this->page->{$this->name} = $this;
		$this->page->save($this->name);
	}

	public function addVote() {

		$post = $this->input->post;

		$value = (int)$post->score;

		if ($value > $this->star_count || $value < 0)
			return;

		$this->vote_sum += (int)$value;
		$this->vote_count++;
	}

	public function userAlreadyVoted() {

		return strpos($this->vote_log, "\n".$this->getUaString()."\n") !== false;
	}

	public function __toString() {

		return $this->render();
	}

	public function getUaString() {

		return $_SERVER['HTTP_USER_AGENT'] . "+" . $_SERVER['REMOTE_ADDR'];
	}
}
