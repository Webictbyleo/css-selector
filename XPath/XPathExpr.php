<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\XPath;

/**
 * XPath expression translator interface.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class XPathExpr implements \Stringable
{
    private $blocked = '';

    /**
     * @param string $path
     * @param string $element
     * @param string $condition
     * @param bool   $starPrefix
     */
    public function __construct(private $path = '', private $element = '*', private $condition = '', $starPrefix = false)
    {
        if ($starPrefix) {
            $this->addStarPrefix();
        }
    }

    /**
     * @return string
     */
    public function getElement()
    {
        return $this->element;
    }
	
	
	public function append($combiner){
		
		$this->condition .= $combiner;
		
		return $this;
	}
	
	public function setBlocked($expr){
		$this->blocked = $expr;
		
		return $this;
	}
	
	
	public function setCondition($condition){
		
		if(!empty($condition)){
			$this->condition= $condition;
		}
		return $this;
	}
    /**
     * @param $condition
     *
     * @return XPathExpr
     */
    public function addCondition($condition)
    {
        $this->condition = $this->condition ? sprintf('%s and (%s)', $this->condition, $condition) : $condition;

        return $this;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }
	
	public function getPath(){
		return $this->path;
	}

    /**
     * @return XPathExpr
     */
    public function addNameTest()
    {
        if ('*' !== $this->element) {
            $this->addCondition('name() = '.Translator::getXpathLiteral($this->element));
            $this->element = '*';
        }

        return $this;
    }

    /**
     * @return XPathExpr
     */
    public function addStarPrefix()
    {
        $this->path .= '*/';

        return $this;
    }

    /**
     * Joins another XPathExpr with a combiner.
     *
     * @param string    $combiner
     *
     * @return XPathExpr
     */
    public function join($combiner, XPathExpr $expr)
    {
        $path = $this->__toString().$combiner;

        if ('*/' !== $expr->path) {
            $path .= $expr->path;
        }

        $this->path = $path;
        $this->element = $expr->element;
        $this->condition = $expr->condition;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
			if(!empty($this->condition)){
			$pr1 = '[';
			$pr2 = ']';
			}
		
        $path = rtrim($this->path,'%ns').'%ns'.$this->element;
		
		if($this->blocked !==false and !str_starts_with($this->path, '//')){
		$path = '//'.$path;	
		$this->blocked = false;
		}
		
        $condition = null === $this->condition || '' === $this->condition ? '' : $this->condition;
		
		if($condition and !in_array($condition,array(':first',':last')) and strrpos($condition,']')!==strlen($condition)-1){
			$condition = $pr1.$condition.$pr2;
		}
		$q = $path.$condition;
			if(!empty($condition) and preg_match('/(\:first|\:last)/',$condition,$m)){
				if($m[1]==':first'){
				$q = '('.str_replace(':first','',$q).')[1]';
				}else{
				$q = '('.str_replace(':last','',$q).')[last()]';
				}
				
			}
		
        return $q;
    }
}
