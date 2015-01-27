<?php

namespace AlterPHP\Mailer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Mailer
{
    /**
     * Mailer
     * @var \Swift_Mailer
     */
    protected $swift ;

    /**
     * Swift_Transport
     * @var \Swift_Transport
     */
    protected $mailerRealTransport ;

    /**
     * TwigEngine
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $twig;

    /**
     * Logger
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Translator
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $t;

    public function __construct(ContainerInterface $container, array $options)
    {
        $this->swift                = $container->get('mailer');
        $this->mailerRealTransport  = $container->get('swiftmailer.transport.real');
        $this->twig                 = $container->get('templating');
        $this->logger               = $container->get('logger');
        $this->t                    = $container->get('translator');

        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    public function getAdminRecipient($name = null)
    {
        $recipientEmail = 'admin@alterphp.com';

        switch ($name) {
            default:
                break;
        }

        return $recipientEmail;
    }

    protected function getFallbackNoReplyEmail()
    {
        $emailFrom = 'contact@alterphp.com';

        return $emailFrom;
    }

    public function mail($to, $type, $name, array $data = array(), array $bcc = array(), array $cc = array(), $from = null)
    {
        if (!isset($from)) {
            $from = $this->options['noReplyEmail'];
        }

        if ($this->options['bccAuto']) {
            $bcc[$this->options['bccAuto']] = $this->options['bccAuto'];
        }

        $message = \Swift_Message::newInstance();

        // Traitement des pièces jointes
        if (isset($data['attachments'])) {
            foreach ($data['attachments'] as $filename => $fileData) {
                $attachment = new \Swift_Attachment($fileData['content'], $filename, $fileData['mime']);
                $message->attach($attachment);
            }
        }

        // Génération body
        $tplFile = sprintf('%s/%s:%s.html.twig', $this->options['tplShortDirectory'], ucfirst($type), $name);
        $body = $this->twig->render($tplFile, $data);

        // Send email
        try {
            $subjectData = array_filter($data, 'is_string');
            $message
                ->setSubject($this->buildSubject($type, $name, $subjectData))
                ->setFrom($from)
                ->setTo($to)
                ->setBody($body, 'text/html')
            ;

            if (!empty($bcc)) {
                $message->setBcc($bcc);
            }
            if (!empty($cc)) {
                $message->setCc($cc);
            }

            $sent = $this->swift->send($message);

            return $sent;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('AlterPHP Mailer: Unable to send email (type: %s, name: %s) to %s', $type, $name, is_string($to) ? $to : var_export($to, true)));
            $this->logger->error(sprintf('AlterPHP Mailer: Exception : %s', $e->getMessage()));
        }
    }

    protected function buildSubject($type, $name, array $subjectData = array())
    {
        $subject = $this->t->trans($type . '.' . $name . '.subject', $subjectData, 'emails');

        return $subject;
    }

    /**
     * Force l'envoi des mails en mode Command
     * @return integer Number of emails sent
     */
    public function forceFlushForCommand()
    {
        // L'envoi des emails est déclenché sur une réponse du Kernel (inactif en mode commande)
        $transport = $this->swift->getTransport();
        if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }
        $spool = $transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        return $spool->flushQueue($this->mailerRealTransport);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'tplShortDirectory',
                'noReplyEmail',
            ))
            ->setDefaults(array(
                'tplShortDirectory'    => 'AppBundle:Email',
                'noReplyEmail'         => $this->getFallbackNoReplyEmail(),
                'bccAuto'              => false,
            ))
            ->setOptional(array(
                'bccAuto',
            ))
            ->setAllowedTypes(array(
                'tplShortDirectory' => 'string',
                'noReplyEmail'      => array('string', 'array'),
                'bccAuto'           => array('bool', 'string'),
            ))
        ;
    }
}
