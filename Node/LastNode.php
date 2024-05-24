<?php

namespace Symfony\Component\CssSelector\Node;

class LastNode extends AbstractNode{
    private NodeInterface $selector;
    private string $identifier;

    public function __construct(NodeInterface $selector, string $identifier){
        $this->selector = $selector;
        $this->identifier = strtolower($identifier);
        
    }

    public function getSelector(): NodeInterface{
        return $this->selector;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSpecificity(): Specificity{
        return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
    }
    public function __toString(): string{
        $r = sprintf('%s[%s:%s][last()]', $this->getNodeName(), $this->selector, $this->identifier);
        return $r;
    }
}