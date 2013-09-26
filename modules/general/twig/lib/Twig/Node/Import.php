<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents an import node.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class Twig_Node_Import extends Twig_Node
{
    public function __construct(Twig_Node_Expression $expr, Twig_Node_Expression_AssignName $var, $lineno, $tag = null)
    {
        parent::__construct(array('expr' => $expr, 'var' => $var), array(), $lineno, $tag);
    }

    public function compile($compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('')
            ->subcompile($this->var)
            ->raw(' = ')
            ->raw('$this->env->loadTemplate(')
            ->subcompile($this->expr)
            ->raw(", true);\n")
        ;
    }
}
