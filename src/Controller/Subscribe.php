<?php

namespace Message\Mothership\Mailing\Controller;

use Message\User\AnonymousUser;

use Message\Cog\Controller\Controller;

class Subscribe extends Controller
{
	public function display()
	{
		return $this->render('Message:Mothership:Mailing::subscribe', array(
			'form' => $this->_getForm(),
		));
	}

	public function action()
	{
		$form = $this->_getForm();

		if ($form->isValid() && $data = $form->getFilteredData()) {
			$this->get('mailing.subscription.edit')->subscribe($data['email']);

			$this->addFlash('success', $this->trans('ms.mailing.subscribe.feedback.success'));
		}

		return $this->redirectToReferer();
	}

	protected function _getForm()
	{
		$form = $this->get('form')
			->setName('mailing-subscribe')
			->setMethod('POST')
			->setAction($this->generateUrl('mailing.subscribe.action'));

		$form->add('email', 'email', $this->trans('ms.mailing.subscribe.email.label'), array(
			'attr' => array(
				'placeholder' => $this->trans('ms.mailing.subscribe.email.placeholder'),
			),
		));

		return $form;
	}
}