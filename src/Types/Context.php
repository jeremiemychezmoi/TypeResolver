<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Types;

/**
 * Provides information about the Context in which the DocBlock occurs that receives this context.
 *
 * A DocBlock does not know of its own accord in which namespace it occurs and which namespace aliases are applicable
 * for the block of code in which it is in. This information is however necessary to resolve Class names in tags since
 * you can provide a short form or make use of namespace aliases.
 *
 * The phpDocumentor Reflection component knows how to create this class but if you use the DocBlock parser from your
 * own application it is possible to generate a Context class using the ContextFactory; this will analyze the file in
 * which an associated class resides for its namespace and imports.
 *
 * @see ContextFactory::createFromClassReflector()
 * @see ContextFactory::createForNamespace()
 */
final class Context
{
    /** @var string The current namespace. */
    private $namespace;

    /** @var array List of namespace aliases => Fully Qualified Namespace. */
    private $namespaceAliases;

    /**
     * Initializes the new context and normalizes all passed namespaces to be in Qualified Namespace Name (QNN)
     * format (without a preceding `\`).
     *
     * @param string $namespace The namespace where this DocBlock resides in.
     * @param array $namespaceAliases List of namespace aliases => Fully Qualified Namespace.
     */
    public function __construct($namespace, array $namespaceAliases = [])
    {
        $this->namespace = ('global' !== $namespace && 'default' !== $namespace)
            ? trim((string)$namespace, '\\')
            : '';

        foreach ($namespaceAliases as $alias => $fqnn) {
            if ($fqnn[0] === '\\') {
                $fqnn = substr($fqnn, 1);
            }
            if ($fqnn[strlen($fqnn) - 1] === '\\') {
                $fqnn = substr($fqnn, 0, -1);
            }

            $namespaceAliases[$alias] = $fqnn;
        }

        $this->namespaceAliases = $namespaceAliases;
    }

    /**
     * Returns the Qualified Namespace Name (thus without `\` in front) where the associated element is in.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Returns a list of Qualified Namespace Names (thus without `\` in front) that are imported, the keys represent
     * the alias for the imported Namespace.
     *
     * @return string[]
     */
    public function getNamespaceAliases()
    {
        return $this->namespaceAliases;
    }
    
    /**
     * Sets a new namespace.
     * 
     * Sets a new namespace for the context. Leading and trailing slashes are
     * trimmed, and the keywords "global" and "default" are treated as aliases
     * to no namespace.
     * 
     * @param string $namespace The new namespace to set.
     * 
     * @return $this
     */
    public function setNamespace($namespace)
    {
        if ('global' !== $namespace
            && 'default' !== $namespace
        ) {
            // Srip leading and trailing slash
            $this->namespace = trim((string)$namespace, '\\');
        } else {
            $this->namespace = '';
        }
        return $this;
    }
    
    /**
     * Sets the namespace aliases, replacing all previous ones.
     * 
     * @param array $namespace_aliases List of namespace aliases => Fully
     *     Qualified Namespace.
     * 
     * @return $this
     */
    public function setNamespaceAliases(array $namespace_aliases)
    {
        $this->namespace_aliases = array();
        foreach ($namespace_aliases as $alias => $fqnn) {
            $this->setNamespaceAlias($alias, $fqnn);
        }
        return $this;
    }
    
    /**
     * Adds a namespace alias to the context.
     * 
     * @param string $alias The alias name (the part after "as", or the last
     *     part of the Fully Qualified Namespace Name) to add.
     * @param string $fqnn  The Fully Qualified Namespace Name for this alias.
     *     Any form of leading/trailing slashes are accepted, but what will be
     *     stored is a name, prefixed with a slash, and no trailing slash.
     * 
     * @return $this
     */
    public function setNamespaceAlias($alias, $fqnn)
    {
        $this->namespace_aliases[$alias] = '\\' . trim((string)$fqnn, '\\');
        return $this;
    }
    
}
