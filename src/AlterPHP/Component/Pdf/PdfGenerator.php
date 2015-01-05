<?php

namespace AlterPHP\Component\Pdf;

use Knp\Snappy\Pdf;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PdfGenerator
{
    /**
     * Generator
     * @var \Knp\Snappy\Pdf
     */
    protected $snappy ;

    /**
     * Router
     */
    protected $router ;

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
        $this->router   = $container->get('router');
        $this->twig     = $container->get('templating');
        $this->logger   = $container->get('logger');
        $this->t        = $container->get('translator');

        $this->kernelDir    = $container->getParameter('kernel.root_dir');

        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    protected function init(array $pdfOptions = [])
    {
        $pdfOptions['footer-html'] = $this->router->generate('pdf_footer', [], true);
        $pdfOptions['footer-line'] = true;
        $pdfOptions['encoding'] = 'UTF-8';

        $this->snappy = new Pdf($this->options['binaryPath'], $pdfOptions);
    }

    public function getPdfContent($name, array $data = array())
    {
        $this->init();
        
        $tplFile = sprintf('%s:%s.pdf.twig', $this->options['tplShortDirectory'], $name);
        $htmlContent = $this->twig->render($tplFile, $data);

        $pdfContent = $this->snappy->getOutputFromHtml($htmlContent);

        return $pdfContent;
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'binaryPath',
                'tplShortDirectory',
            ))
            ->setDefaults(array(
                'binaryPath' => $this->kernelDir . '/../vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64',
                'tplShortDirectory' => 'AppBundle:Pdf',
            ))
            ->setAllowedTypes(array(
                'binaryPath' => 'string',
                'tplShortDirectory' => 'string',
            ))
        ;
    }
}
