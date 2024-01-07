<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\XPath\Extension;

use Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use Symfony\Component\CssSelector\XPath\XPathExpr;

/**
 * XPath expression translator pseudo-class extension.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class PseudoClassExtension extends AbstractExtension
{
    public function getPseudoClassTranslators(): array
    {
        return [
            'root' => $this->translateRoot(...),
            'scope' => $this->translateScopePseudo(...),
            'first-child' => $this->translateFirstChild(...),
            'last-child' => $this->translateLastChild(...),
            'first-of-type' => $this->translateFirstOfType(...),
            'last-of-type' => $this->translateLastOfType(...),
            'only-child' => $this->translateOnlyChild(...),
            'only-of-type' => $this->translateOnlyOfType(...),
            'empty' => $this->translateEmpty(...),
            //Custom classes
			'first'=> $this->translateFirst(...),
			'last'=> $this->translateLast(...),
            'button'=> $this->translateButton(...),
			'submit'=> $this->translateSubmit(...),
			'parent'=> $this->translateParent(...),
			'has'=> $this->translateHas(...),
			'visible'=> $this->translateVisible(...),
			'hidden'=> $this->translateHidden(...),
			'file'=> $this->translateFile(...),
			'selected'=> $this->translateSelected(...),
			'header'=> $this->translateHeader(...),
			'input'=> $this->translateInput(...),
			'text'=> $this->translateText(...),
			'radio'=> $this->translateRadio(...),
			'checked'=> $this->translateChecked(...),
			'disabled'=> $this->translateDisabled(...),
			'checkbox'=> $this->translateCheckbox(...),
			'odd'=> $this->translateOdd(...),
			'even'=> $this->translateEven(...),
			'contains'=>$this->translateContains(...),
        ];
    }

    public function translateRoot(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition('not(parent::*)');
    }

    public function translateScopePseudo(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition('1');
    }

    public function translateFirstChild(XPathExpr $xpath): XPathExpr
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('position() = 1');
    }

    public function translateLastChild(XPathExpr $xpath): XPathExpr
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('position() = last()');
    }

    /**
     * @throws ExpressionErrorException
     */
    public function translateFirstOfType(XPathExpr $xpath): XPathExpr
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:first-of-type" is not implemented.');
        }

        return $xpath
            ->addStarPrefix()
            ->addCondition('position() = 1');
    }

    /**
     * @throws ExpressionErrorException
     */
    public function translateLastOfType(XPathExpr $xpath): XPathExpr
    {
        if ('*' === $xpath->getElement()) {
            throw new ExpressionErrorException('"*:last-of-type" is not implemented.');
        }

        return $xpath
            ->addStarPrefix()
            ->addCondition('position() = last()');
    }

    public function translateOnlyChild(XPathExpr $xpath): XPathExpr
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('last() = 1');
    }

    public function translateOnlyOfType(XPathExpr $xpath): XPathExpr
    {
        $element = $xpath->getElement();

        return $xpath->addCondition(sprintf('count(preceding-sibling::%s)=0 and count(following-sibling::%s)=0', $element, $element));
    }

    public function translateEmpty(XPathExpr $xpath): XPathExpr
    {
        return $xpath->addCondition('not(*) and not(string-length())');
    }
    public function translateFirst(XPathExpr $xpath)
    {
		//return $xpath->addCondition('((string-length() > 0) or (*))');
			$path = $xpath->getCondition();
			
			if(!empty($path)){
				
				return $xpath->append(':first');
				
			}else{
				
				return $xpath->append(':first');
			
			}
		
    }
	 public function translateLast(XPathExpr $xpath)
    {
        $path = $xpath->getCondition();
			if(!empty($path)){
				$xpath->append(':last');
			}else{
				 $xpath->append(':last');
			}
       
		return $xpath;
    }
    public function translateButton(XPathExpr $xpath)
    {
        return $xpath->addCondition("(name(.) = 'button' or (name(.) = 'input' and @type = 'button'))");
    }
    public function translateSubmit(XPathExpr $xpath)
    {
        return $xpath->addCondition("((name(.) = 'button' and @type = 'submit') or (name(.) = 'input' and @type = 'submit'))");
    }
    public function translateParent(XPathExpr $xpath)
    {
        return $xpath->addCondition("((string-length() > 0) or (*))");
    }
    public function translateHas(XPathExpr $xpath)
    {
        return $xpath->addCondition("(descendant::*[not(string-length(name(.))=0)])");
    }
    public function translateVisible(XPathExpr $xpath){
		$l = "(name(.)= 'input' and @type = 'hidden')
		 or name(.)= 'option' 
		 or name(.)= 'title' 
		 or name(.)= 'script' 
		 or name(.)= 'head' 
		 or name(.)= 'meta' 
		 or name(.)= 'link' 
		 or (@width = 0 or @height = 0) 
		 or (contains(@style,'width:0') or contains(@style,'opacity:0') or contains(@style,'height:0'))";
		return $xpath->addCondition("not(
		$l 
		 or ancestor::*[$l]
		 )");
	}
    public function translateHidden(XPathExpr $xpath)
    {
		$l = "(name(.)= 'input' and @type = 'hidden')
		 or name(.)= 'option' 
		 or name(.)= 'title' 
		 or name(.)= 'script' 
		 or name(.)= 'head' 
		 or name(.)= 'meta' 
		 or name(.)= 'link' 
		 or (@width = 0 or @height = 0)
		 or (contains(@style,'width:0') or contains(@style,'opacity:0') or contains(@style,'height:0')) ";
        return $xpath->addCondition("
		 (
		 $l
		 or ancestor::*[$l]
		 )
		
		");
    }
    public function translateFile(XPathExpr $xpath)
    {
        return $xpath->addCondition("(name(.) = 'input' and @type = 'file')");
    }
    public function translateSelected(XPathExpr $xpath)
    {
        return $xpath->addCondition("((@selected and name(.) = 'option') and parent::*[name(.) = 'select'])");
    }
    public function translateHeader(XPathExpr $xpath)
    {
        return $xpath->addCondition("(name(.) = 'h1' or name(.) = 'h2' or name(.)= 'h3' or name(.) = 'h4' or  name(.) = 'h5' or name(.) = 'h6')");
    }
    public function translateInput(XPathExpr $xpath)
    {
        return $xpath->addCondition(
		 '('
                .'('
                    ."(name(.) = 'input')"
                    ." or name(.) = 'button'"
                    ." or name(.) = 'select'"
                    ." or name(.) = 'textarea'"
                .')'
            .')'
		);
    }
    public function translateText(XPathExpr $xpath)
    {
        return $xpath->addCondition("(name(.) = 'input' and @type = 'text')");
    }
    public function translateRadio(XPathExpr $xpath)
    {
        return $xpath->addCondition("(name(.) = 'input' and @type = 'radio')");
    }
	public function translateChecked(XPathExpr $xpath)
    {
        return $xpath->addCondition(
            '(@checked '
            ."and (name(.) = 'input' or name(.) = 'command')"
            ."and (@type = 'checkbox' or @type = 'radio'))"
        );
    }
    public function translateDisabled(XPathExpr $xpath)
    {
        return $xpath->addCondition(
            '('
                .'@disabled and'
                .'('
                    ."(name(.) = 'input' and @type != 'hidden')"
                    ." or name(.) = 'button'"
                    ." or name(.) = 'select'"
                    ." or name(.) = 'textarea'"
                    ." or name(.) = 'command'"
                    ." or name(.) = 'fieldset'"
                    ." or name(.) = 'optgroup'"
                    ." or name(.) = 'option'"
                .')'
            .') or ('
                ."(name(.) = 'input' and @type != 'hidden')"
                ." or name(.) = 'button'"
                ." or name(.) = 'select'"
                ." or name(.) = 'textarea'"
            .')'
            .' and ancestor::fieldset[@disabled]'
        );
        // todo: in the second half, add "and is not a descendant of that fieldset element's first legend element child, if any."
    }
    public function translateCheckbox(XPathExpr $xpath)
    {
        return $xpath->addCondition("(name(.) = 'input' and @type = 'checkbox')");
    }
    public function translateOdd(XPathExpr $xpath)
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('not(position() mod 2=0)');
    }
    public function translateEven(XPathExpr $xpath)
    {
        return $xpath
            ->addStarPrefix()
            ->addNameTest()
            ->addCondition('position() mod 2=0');
    }
    public function translateContains(XPathExpr $xpath){
        return $xpath->addCondition("(descendant::text()[string-length(normalize-space(.)) > 0])");
   }

    public function getName(): string
    {
        return 'pseudo-class';
    }
}
