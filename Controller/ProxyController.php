<?php

namespace Ikadoc\KCFinderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProxyController extends AbstractController
{
    protected ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function proxyAction(Request $request, string $file): Response
    {
        $pathinfo = pathinfo($file);
        $path = $pathinfo['dirname'];
        $fileName = $pathinfo['basename'];
        if ('.' == $path) {
            $path = $this->parameterBag->get('ikadoc_kc_finder_path');
        } else {
            $path = rtrim($this->parameterBag->get('ikadoc_kc_finder_path'), '/').'/'.$path;
        }

        if (in_array($pathinfo['extension'], ['png', 'gif', 'jpg', 'ico'])) {
            return new BinaryFileResponse($path.'/'.$pathinfo['basename']);
        }

        $config = $this->parameterBag->get('ikadoc_kc_finder_config');
        $config['cookieDomain'] = $request->getHost();
        if (is_array($config)) {
            if (!array_key_exists('KCFINDER', $_SESSION) || is_null($_SESSION['KCFINDER'])) {
                $_SESSION['KCFINDER'] = [];
            }
            foreach ($config as $configName => $configElement) {
                $_SESSION['KCFINDER'][$configName] = $configElement;
            }
        }

        $previousScriptFileName = $_SERVER['SCRIPT_FILENAME'];
        $previousCwd = getcwd();
        chdir($path);
        $_SERVER['SCRIPT_FILENAME'] = $path.'/'.$fileName;

        require $pathinfo['basename'];

        $_SERVER['SCRIPT_FILENAME'] = $previousScriptFileName;
        chdir($previousCwd);

        ob_end_flush();

        return new Response();
    }
}
