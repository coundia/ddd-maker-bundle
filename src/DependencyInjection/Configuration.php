<?php declare(strict_types=1);
/**
 * This file holds bundle configuration.
 */

namespace Cnd\DddMakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface{
	public function getConfigTreeBuilder(): TreeBuilder{
		$treeBuilder = new TreeBuilder('crud_maker');
		$treeBuilder->getRootNode();
		return $treeBuilder;
	}
}
