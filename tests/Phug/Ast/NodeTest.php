<?php

namespace Phug\Test\Ast;

use Phug\Ast\Node;
use Phug\Ast\NodeInterface;

//@codingStandardsIgnoreStart
class A extends Node
{
}
class B extends Node
{
}
class C extends Node
{
}
class D extends Node
{
}

/**
 * @coversDefaultClass Phug\Ast\Node
 */
class NodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::appendChild
     * @covers ::prependChild
     * @covers ::getIndex
     * @covers ::getChildAt
     */
    public function testAppendChild()
    {

        $node = new Node();

        $node->appendChild($a = new A());
        $b = new B($node);
        $node->prependChild($c = new C());
        $node->appendChild($d = new D());

        $this->assertEquals(1, $a->getIndex());
        $this->assertEquals(2, $b->getIndex());
        $this->assertEquals(0, $c->getIndex());
        $this->assertEquals(3, $d->getIndex());
        $this->assertInstanceOf(A::class, $node->getChildAt(1));
        $this->assertInstanceOf(B::class, $node->getChildAt(2));
        $this->assertInstanceOf(C::class, $node->getChildAt(0));
        $this->assertInstanceOf(D::class, $node->getChildAt(3));
    }

    /**
     * @covers ::appendChild
     * @covers ::prependChild
     * @covers ::remove
     * @covers ::getChildren
     */
    public function testRemovalResetsOffsetsCorrectly()
    {

        $node = new Node();

        $node->appendChild($a = new A());
        $b = new B($node);
        $node->prependChild($c = new C());
        $node->appendChild($d = new D());

        $a->remove();

        $this->assertSame([$c, $b, $d], $node->getChildren());
    }

    /**
     * @covers ::appendChild
     * @covers ::prependChild
     * @covers ::remove
     * @covers ::getPreviousSibling
     * @covers ::getNextSibling
     */
    public function testSiblings()
    {

        $node = new Node();

        $node->appendChild($a = new A());
        $b = new B($node);
        $node->prependChild($c = new C());
        $node->appendChild($d = new D());

        $a->remove();

        $this->assertSame(null, $c->getPreviousSibling());
        $this->assertSame($b, $c->getNextSibling());
        $this->assertSame(null, $d->getNextSibling());
        $this->assertSame($b, $d->getPreviousSibling());
        $this->assertSame($c, $b->getPreviousSibling());
        $this->assertSame($d, $b->getNextSibling());
    }

    /**
     * @covers ::appendChild
     * @covers ::findChildrenArray
     * @covers ::isInstanceOf
     * @covers ::getNextSibling
     */
    public function testFindChildren()
    {

        $node = new Node();

        $node->appendChild(new A())
            ->appendChild((new B())->appendChild(new B()))
            ->appendChild(new B())
            ->appendChild(new D())
            ->appendChild((new B())->appendChild((new B())->appendChild(new B())))
            ->appendChild(new C())
            ->appendChild(new D())
            ->appendChild(new C())
            ->appendChild(new D())
            ->appendChild(new C())
            ->appendChild(new D());

        $aChildren = $node->findChildrenArray(function (NodeInterface $node) {

            return $node->isInstanceOf(A::class);
        });

        $bDeepChildren = $node->findChildrenArray(function (NodeInterface $node) {

            return $node->isInstanceOf(B::class);
        });

        $bFirstChildren = $node->findChildrenArray(function (NodeInterface $node) {

            return $node->isInstanceOf(B::class);
        }, 0);

        $bSecondChildren = $node->findChildrenArray(function (NodeInterface $node) {

            return $node->isInstanceOf(B::class);
        }, 1);

        $cChildren = $node->findChildrenArray(function (NodeInterface $node) {

            return $node->isInstanceOf(C::class);
        });

        $dChildren = $node->findChildrenArray(function (NodeInterface $node) {

            return $node->isInstanceOf(D::class);
        });

        $this->assertCount(1, $aChildren, 'A children');
        $this->assertCount(6, $bDeepChildren, 'B deep children');
        $this->assertCount(3, $bFirstChildren, 'B first level');
        $this->assertCount(5, $bSecondChildren, 'B 2 levels');
        $this->assertCount(3, $cChildren, 'C children');
        $this->assertCount(4, $dChildren, 'D children');
    }
}
//@codingStandardsIgnoreEnd
