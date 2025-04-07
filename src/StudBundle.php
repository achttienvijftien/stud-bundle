<?php

namespace AchttienVijftien\Bundle\StudBundle;

use AchttienVijftien\Bundle\StudBundle\Compiler\FireHooksPass;
use AchttienVijftien\Bundle\StudBundle\Compiler\RegistrableTypePass;
use AchttienVijftien\Stud\Fire\Attribute\OnHook;
use AchttienVijftien\Stud\Fire\FireHooks;
use AchttienVijftien\Stud\Fire\HookIterator;
use Symfony\Component\DependencyInjection\ChildDefinition;
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

	/**
	 * Builds bundle.
	 *
	 * @param ContainerBuilder $container The container builder.
	 *
	 * @return void
	 */
	public function build( ContainerBuilder $container ): void {
		parent::build( $container );

		$container->addCompilerPass( new RegistrableTypePass() );

		$container->registerAttributeForAutoconfiguration(
			OnHook::class,
			static function ( ChildDefinition $definition, OnHook $attribute, \ReflectionClass $reflector ): void {
				$definition->addTag( 'stud.fire_hook', $attribute->to_array() );
			}
		);

		$container->addCompilerPass( new FireHooksPass() );
	}

	/**
	 * Boots bundle.
	 *
	 * @return void
	 */
	public function boot(): void {
		$hooks = $this->container->get( FireHooks::class );
		$hooks->add_hooks();
	}
}
