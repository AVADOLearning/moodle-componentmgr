<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\DependencyInjection;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add tagged console commands.
 */
class ConsoleCommandsPass implements CompilerPassInterface {
    /**
     * Console command class.
     *
     * @var string
     */
    const CONSOLE_COMMAND = 'Symfony\\Component\\Console\\Command\\Command';

    /**
     * Method to call on console application to add commands.
     *
     * @var string
     */
    const ADD_METHOD = 'add';

    /**
     * Console application ID.
     *
     * @var string
     */
    protected $appId;

    /**
     * Tag name.
     *
     * @var string
     */
    protected $tagName;

    /**
     * Initialiser.
     *
     * @param string|null $tagName
     * @param string|null $appId
     */
    public function __construct($tagName=null, $appId=null) {
        $this->tagName = ($tagName === null) ? 'console.command' : $tagName;
        $this->appId = ($appId === null) ? 'console.application' : $appId;
    }

    /**
     * @inheritdoc CompilerPassInterface
     */
    public function process(ContainerBuilder $container) {
        $services      = $container->findTaggedServiceIds($this->tagName);
        $appDefinition = $container->getDefinition($this->appId);

        foreach ($services as $id => $tags) {
            $definition = $container->getDefinition($id);

            if ($definition->isAbstract()) {
                throw new InvalidArgumentException(sprintf(
                        'The service "%s" tagged "%s" must not be abstract.',
                        $id, $this->tagName));
            }

            $class = $container->getParameterBag()->resolveValue(
                    $definition->getClass());
            if (!is_subclass_of($class, static::CONSOLE_COMMAND)) {
                throw new InvalidArgumentException(sprintf(
                        'The service "%s" tagged "%s" must be a subclass of "%s".',
                        $id, $this->tagName, static::CONSOLE_COMMAND));
            }

            $appDefinition->addMethodCall(
                    static::ADD_METHOD, [new Reference($id)]);
        }
    }
}
