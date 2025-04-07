<?php

namespace AchttienVijftien\Bundle\StudBundle\Compiler;

use AchttienVijftien\Stud\Fire\Attribute\OnHook;
use AchttienVijftien\Stud\Fire\FireHooks;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class FireHooksPass
 *
 * @package AchttienVijftien\Bundle\StudBundle\Compiler
 */
class FireHooksPass implements CompilerPassInterface {

	/**
	 * List of service references.
	 *
	 * @var array
	 */
	private array $references = [];

	/**
	 * Process during container compiling.
	 *
	 * @param ContainerBuilder $container The container builder.
	 *
	 * @return void
	 */
	public function process( ContainerBuilder $container ) {
		$fireable_services  = $container->findTaggedServiceIds( 'stud.fireable' );
		$init_hook_services = $container->findTaggedServiceIds( 'stud.fire_hook' );
		$hook_iterator      = $container->findDefinition( FireHooks::class );

		$default_hook = new OnHook( 'plugins_loaded' );
		foreach ( $fireable_services as $id => $tags ) {
			if ( ! isset( $tags[0]['tag'] ) ) {
				continue;
			}

			$fireable = $this->get_reference( $id );

			$service_services = $container->findTaggedServiceIds( $tags[0]['tag'] );
			foreach ( $service_services as $service_id => $service_tags ) {
				if ( isset( $init_hook_services[ $service_id ][0] ) ) {
					$hook = OnHook::from_array( $init_hook_services[ $service_id ][0] );
				} elseif ( isset( $tags[0]['default_hook'] ) ) {
					$hook = OnHook::from_array(
						is_string( $tags[0]['default_hook'] )
							? [ 'hook' => $tags[0]['default_hook'] ]
							: $tags[0]['default_hook']
					);
				} else {
					$hook = clone $default_hook;
				}

				$hook_iterator = $hook_iterator->addMethodCall( 'add_service', [
					$hook->hook,
					$hook->priority,
					$fireable,
					$this->get_reference( $service_id ),
				] );
			}
		}
	}

	/**
	 * Gets reference of service.
	 *
	 * @param string $id The id of the service.
	 *
	 * @return Reference
	 */
	private function get_reference( string $id ): Reference {
		if ( ! isset( $this->references[ $id ] ) ) {
			$this->references[ $id ] = new Reference( $id );
		}

		return $this->references[ $id ];
	}
}
