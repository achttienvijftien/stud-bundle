<?php

namespace AchttienVijftien\Bundle\StudBundle;

use AchttienVijftien\Bundle\StudBundle\Compiler\RegistrableTypePass;
use AchttienVijftien\Stud\Boot;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Class StudBundle.
 *
 * @package AchttienVijftien\Bundle\StudBundle
 */
class StudBundle extends AbstractBundle {
	/**
	 * @param array $config
	 * @param ContainerConfigurator $container
	 * @param ContainerBuilder $builder
	 *
	 * @return void
	 */
	public function loadExtension( array $config, ContainerConfigurator $container, ContainerBuilder $builder ): void {
		$container->import( '../config/services.yaml' );
	}

	public function build( ContainerBuilder $container ): void {
		parent::build( $container );

		$container->addCompilerPass( new RegistrableTypePass() );
	}

	/**
	 * Boots bundle.
	 *
	 * @return void
	 */
	public function boot(): void {
		$this->container->get( Boot::class );
	}
}
