<?php namespace BookStack\Listeners;

use BookStack\Auth\Access\Saml2Service;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Illuminate\Support\Facades\Log;

class Saml2LoginEventListener
{
    protected $saml;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Saml2Service $saml)
    {
        $this->saml = $saml;
    }

    /**
     * Handle the event.
     *
     * @param  Saml2LoginEvent  $event
     * @return void
     */
    public function handle(Saml2LoginEvent $event)
    {
        $messageId = $event->getSaml2Auth()->getLastMessageId();
        // TODO: Add your own code preventing reuse of a $messageId to stop replay attacks

        $samlUser = $event->getSaml2User();

        $attrs = $samlUser->getAttributes();
        $id    = $samlUser->getUserId();
        //$assertion = $user->getRawSamlAssertion()

        $user = $this->saml->processLoginCallback($id, $attrs);
    }
}
