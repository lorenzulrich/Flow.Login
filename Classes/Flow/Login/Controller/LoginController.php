<?php
namespace Flow\Login\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flow.Login".  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A controller which allows for loggin into a application
 *
 * @Flow\Scope("singleton")
 */
class LoginController extends \TYPO3\Flow\Security\Authentication\Controller\AbstractAuthenticationController {

	/**
	 * @var array
	 */
	protected $supportedMediaTypes = array('text/html', 'application/json', 'application/jsonp');

	/**
	 * @var array
	 */
	protected $viewFormatToObjectNameMap = array(
		'html'  => 'TYPO3\Fluid\View\TemplateView',
		'json'  => 'TYPO3\Flow\Mvc\View\JsonView');

	/**
	 * @var \TYPO3\Flow\I18n\Translator
	 * @Flow\Inject
	 */
	protected $translator;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Authorization\AccessDecisionManagerInterface
	 */
	protected $accessDecisionManager;

	/**
	 * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
	 * @Flow\Inject
	 */
	protected $authenticationManager;

	/**
	 * Index action
	 *
	 * @param string $username
	 * @return void
	 */
	public function indexAction($username = NULL) {
		if ($this->authenticationManager->isAuthenticated()) {
			if(isset($this->settings['Redirect']['signedIn'])) {
				$redirect = $this->settings['Redirect']['signedIn'];
				$this->redirect($redirect['actionName'], $redirect['controllerName'], $redirect['packageKey']);
			}
			$this->redirect('signedIn');
		}
		$this->view->assign('username', $username);
		$this->view->assign('hostname', $this->request->getHttpRequest()->getBaseUri()->getHost());
		$this->view->assign('date', new \DateTime());
	}

	/**
	 * Loginpanel action
	 *
	 * @param string $username
	 * @return void
	 */
	public function loginPanelAction($username = NULL) {
		$this->view->assign('username', $username);
		$this->view->assign('hostname', $this->request->getHttpRequest()->getBaseUri()->getHost());
		$this->view->assign('date', new \DateTime());
	}

	/**
	 * SignedIn dummy action to show you that it works
	 *
	 * @return void
	 */
	public function signedInAction(){
	}

	/**
	 * Redirect action
	 *
	 * @return void
	 */
	public function redirectAction() {
		$this->redirect('index');
	}

	/**
	 * Is called if authentication was successful.
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
	 * @return string
	 */
	public function onAuthenticationSuccess(\TYPO3\Flow\Mvc\ActionRequest $originalRequest = NULL) {
		$uriBuilder = $this->controllerContext->getUriBuilder();
		if ($originalRequest !== NULL) {
			$uri =  $uriBuilder->uriFor($originalRequest->getControllerActionName(), $originalRequest->getArguments(), $originalRequest->getControllerName(), $originalRequest->getControllerPackageKey());
		} else {
			if(isset($this->settings['Redirect']['signedIn'])) {
				$packageKey     = $this->settings['Redirect']['signedIn']['packageKey'];
				$controllerName = $this->settings['Redirect']['signedIn']['controllerName'];
				$actionName     = $this->settings['Redirect']['signedIn']['actionName'];
				$uri = $uriBuilder->uriFor($actionName, NULL, $controllerName, $packageKey);
			} else {
				$uri = $uriBuilder->uriFor('signIn', NULL, 'Login', 'Flow.Login');
			}
		}

		$response = array();
		$response['status'] = 'OK';
		$response['redirect'] = $uri;

		$this->view->assign('value', $response);
	}

	/**
	 * Logs out a - possibly - currently logged in account.
	 *
	 * @return void
	 */
	public function logoutAction() {
		parent::logoutAction();

		switch ($this->request->getFormat()) {
			default :
				$this->flashMessageContainer->addMessage(new \TYPO3\Flow\Error\Message('Successfully logged out.', 1318421560));
				$this->redirect('index');
			break;
		}
	}

//	/**
//	 * A template method for displaying custom error flash messages, or to
//	 * display no flash message at all on errors. Override this to customize
//	 * the flash message in your action controller.
//	 *
//	 * This method is used to overwrite default error messages template TYPO3.Flow package
//	 *
//	 * @return \TYPO3\Flow\Error\Error The flash message
//	 */
//	protected function getErrorFlashMessage() {
//		/*return new \TYPO3\Flow\Error\Message(
//			$this->translator->translateById('message.authentication.failed.wrongCredentials', array(), NULL, NULL, 'Main', 'Flow.Login'),
//			NULL,
//			array(),
//			$this->translator->translateById('message.authentication.failed', array(), NULL, NULL, 'Main', 'Flow.Login')
//		);
//		*/
//	}

	/**
	 * Collects the errors and serves them
	 *
	 * @return void
	 */
	protected function errorAction() {
			// Create response array
			// @todo translations
		$response = array();
		$response['status'] = 'FAILED';
		$response['errors'] = $this->flashMessageContainer->getMessagesAndFlush();
		$this->view->assign('value', $response);
	}

}

?>