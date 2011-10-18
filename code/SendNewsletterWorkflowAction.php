<?php
/**
 * A simple workflow action that sends the newsletter.
 *
 * @package silverstripe-newsletter-advancedworkflow
 */
class SendNewsletterWorkflowAction extends WorkflowAction {

	public static $icon = 'newsletter-advancedworkflow/images/send-newsletter.png';

	public function execute(WorkflowInstance $workflow) {
		$newsletter = $workflow->getTarget();

		if (!$newsletter instanceof Newsletter) {
			throw new Exception('An invalid target class was encountered.');
		}

		$type = $newsletter->getNewsletterType();
		$from = $type && $type->FromEmail ? $type->FromEmail : Email::getAdminEmail();

		if ($newsletter->Status == 'Draft') {
			$process = new NewsletterEmailProcess(
				$newsletter->Subject,
				$from,
				$newsletter,
				$type,
				base64_encode($newsletter->ID . '_' . date('d-m-Y H:i:s')),
				$type->Group()->Members()
			);
			$process->start();
		}

		return true;
	}

}
