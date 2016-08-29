<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
			/****************************************
			*				Symfony 				*
			*****************************************/
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			/****************************************
			*			Vendor - Doctrine			*
			*****************************************/
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
			new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
			/****************************************
			*			Vendor - FOS				*
			*****************************************/
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\CommentBundle\FOSCommentBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            

            
            new Ob\HighchartsBundle\ObHighchartsBundle(),
			new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            
			new JMS\SerializerBundle\JMSSerializerBundle($this),

			/****************************************
			*				M-GaTE					*
			*****************************************/
			new mgate\UserBundle\mgateUserBundle(),
			new mgate\PubliBundle\mgatePubliBundle(),
            new mgate\DashboardBundle\mgateDashboardBundle(),
            new mgate\StatBundle\mgateStatBundle(),
            new mgate\TresoBundle\mgateTresoBundle(),
            new mgate\FormationBundle\mgateFormationBundle(),
			new mgate\PersonneBundle\mgatePersonneBundle(),
            new mgate\CommentBundle\mgateCommentBundle(),
			new mgate\SuiviBundle\mgateSuiviBundle(),
			new JavierEguiluz\Bundle\EasyAdminBundle\EasyAdminBundle(),
            new n7consulting\RhBundle\n7consultingRhBundle(),
            new n7consulting\DevcoBundle\n7consultingDevcoBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
