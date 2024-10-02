<?php

namespace AchttienVijftien\Bundle\StudBundle\Compiler;

use AchttienVijftien\Stud\Structure\Registrable;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegistrableTypePass.
 *
 * @package AchttienVijftien\Bundle\StudBundle\Compiler
 */
class RegistrableTypePass implements CompilerPassInterface {

	/**
	 * Process during container compiling.
	 *
	 * @param ContainerBuilder $container The container builder.
	 *
	 * @return void
	 */
	public function process( ContainerBuilder $container ): void {
		if ( ! $container->has( Registrable::class ) ) {
			return;
		}

		$definition   = $container->findDefinition( Registrable::class );
		$registrables = $container->findTaggedServiceIds( 'stud.registrable' );

		foreach ( $registrables as $id => $tags ) {
			foreach ( $tags as $attributes ) {
				$definition->addMethodCall( 'add_registrable', [
					new Reference( $id ),
					$attributes['type'],
				] );
			}
		}
	}
}
