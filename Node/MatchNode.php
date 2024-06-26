<?php

namespace Symfony\Component\CssSelector\Node;

class MatchNode extends AbstractNode{
    private NodeInterface $selector;
    private NodeInterface $subSelector;

    public function __construct(NodeInterface $selector, NodeInterface $subSelector){
        $this->selector = $selector;
        $this->subSelector = $subSelector;
    }

    public function getSelector(): NodeInterface{
        return $this->selector;
    }

    public function getSubSelector(): NodeInterface{
        return $this->subSelector;
    }

    public function getSpecificity(): Specificity{
        return $this->selector->getSpecificity()->plus($this->subSelector->getSpecificity());
    }

    public function __toString(): string{
        return sprintf('%s[%s:is(%s)]', $this->getNodeName(), $this->selector, $this->subSelector);
    }
}