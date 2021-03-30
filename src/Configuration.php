<?php declare(strict_types=1);

namespace Kiboko\Plugin\JSON;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Kiboko\Plugin\Log;

final class Configuration implements ConfigurationInterface
{
    private ConfigurationInterface $loggerConfiguration;

    public function __construct(?ConfigurationInterface $loggerConfiguration = null)
    {
        $this->loggerConfiguration = $loggerConfiguration ?: new Log\Configuration();
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $extractor = new Configuration\Extractor();
        $loader = new Configuration\Loader();

        $builder = new TreeBuilder('json');
        $builder->getRootNode()
            ->validate()
                ->ifTrue(function (array $value) {
                    return array_key_exists('extractor', $value) && array_key_exists('loader', $value);
                })
                ->thenInvalid('Your configuration should either contain the "extractor" or the "loader" key, not both.')
            ->end()
            ->children()
                ->append(node: $extractor->getConfigTreeBuilder()->getRootNode())
                ->append(node: $loader->getConfigTreeBuilder()->getRootNode())
                ->append(node: $this->loggerConfiguration->getConfigTreeBuilder()->getRootNode())
            ->end()
        ;

        return $builder;
    }
}
