<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * PHP version 5
 *
 * @package     omeka
 * @subpackage  nlfeatures
 * @author      Scholars' Lab <>
 * @author      Eric Rochester <erochest@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?><?php

require_once 'NeatlineFeatures_Test.php';
require_once 'application/helpers/FormFunctions.php';
require_once 'lib/NeatlineFeatures/Utils/View.php';

/**
 * This tests the utility class for views.
 **/
class NeatlineFeatures_Utils_View_Test extends NeatlineFeatures_Test
{

    /**
     * The title element.
     *
     * @var Element
     **/
    var $_title;

    /**
     * The subject element.
     *
     * @var Element
     **/
    var $_subject;

    /**
     * The coverage element.
     *
     * @var Element
     **/
    var $_coverage;

    /**
     * The NeatlineFeatures_Utils_View for the coverage element.
     *
     * @var NeatlineFeatures_Utils_View
     **/
    var $_cutil;

    /**
     * This is an item to play with.
     *
     * @var Item
     **/
    var $_item;

    /**
     * This performs a little set up for this set of tests.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function setUp()
    {
        parent::setUp();

        $rows = $this
            ->db
            ->getTable('Element')
            ->findBy(array('name' => 'Coverage'));

        foreach ($rows as $row) {
            switch ($row->name) {
            case 'Coverage':
                $this->_coverage = $row;
                $this->_cutil = new NeatlineFeatures_Utils_View();
                $this->_cutil->setEditOptions(
                    'Elements[38][0]', null, array(), null, $row
                );
                break;
            case 'Title':
                $this->_title = $row;
                break;
            case 'Subject':
                $this->_subject = $row;
                break;
            }
        }

        $this->_item = new Item;
        $this->_item->save();

        $this->addElementText($this->_item, $this->_title, '<b>A Title</b>',
            TRUE);
        $this->addElementText($this->_item, $this->_subject, 'Subject');

        $this->_item->save();
    }

    /**
     * Tear everything back down.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function tearDown()
    {
        parent::tearDown();
        if (isset($this->_item['title'])) {
            $this->_item['title']->delete();
        }
        if (isset($this->_item['subject'])) {
            $this->_item['subject']->delete();
        }
        $this->_item->delete();
        $this->_item = null;
    }

    /**
     * This tests pulling the element ID from $inputNameStem
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testElementId()
    {
        $this->assertEquals(38, $this->_cutil->getElementId());
        $util = new NeatlineFeatures_Utils_View();
        $util->setEditOptions("Elements[50][0]", null,
                              array(), null, $this->_title);
        $this->assertEquals(50, $util->getElementId());
    }

    /**
     * This tests pulling the index from $inputNameStem.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetIndex()
    {
        $util = new NeatlineFeatures_Utils_View();
        $util->setEditOptions("Elements[38][1]", null, array(), null, null);
        $this->assertEquals(1, $util->getIndex());
        $util = new NeatlineFeatures_Utils_View();
        $util->setEditOptions("Elements[38][3]", null, array(), null, null);
        $this->assertEquals(3, $util->getIndex());
    }

    /**
     * This tests the TEXTAREA returned by getFreeField.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetFreeField()
    {
        $expected = new DOMDocument;
        $expected->loadXML(
            '<textarea id="Elements-38-0-free" name="Elements[38][0][free]" ' .
            'class="textinput" rows="5" cols="50"></textarea>'
        );

        $actual = new DOMDocument;
        $actual->loadXML($this->_cutil->getFreeField());

        $this->assertEqualXMLStructure(
            $expected->firstChild, $actual->firstChild, TRUE
        );
    }

    /**
     * This tests whether the text field is created as a hidden field.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetTextField()
    {
        $expected = new DOMDocument;
        $expected->loadXML(
            '<input type="hidden" id="Elements-38-0-text" name="Elements[38][0][text]" ' .
            'value="" />'
        );

        $actual = new DOMDocument;
        $actual->loadHTML($this->_cutil->getTextField());
        $input = $actual->getElementsByTagName('input')->item(0);

        $this->assertEqualXMLStructure($expected->firstChild, $input, TRUE);
    }

    /**
     * This tests the predicate for whether this is submitted using POST or 
     * not.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsPosted()
    {
        $this->assertFalse($this->_cutil->isPosted());
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            'o' => 'oops'
        );
        $this->assertTrue($this->_cutil->isPosted());
    }

    /**
     * This tests getHtmlValue, which returns the Elements[id][n][html] field 
     * from the POST request.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetHtmlValue()
    {
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array('html' => '1')
        );
        $this->assertEquals('1', $this->_cutil->getHtmlValue());

        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array('html' => '3')
        );
        $this->assertEquals('3', $this->_cutil->getHtmlValue());
    }

    /**
     * This tests the getElementText function, which is a wrapper around the 
     * same function from the view helper.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetElementText()
    {
        $tutil = new NeatlineFeatures_Utils_View();
        $tutil->setEditOptions(
            "Elements[{$this->_title->id}][0]", '<b>A Title</b>', array(),
            $this->_item, $this->_title);
        $etext = $tutil->getElementText();
        $this->assertEquals('<b>A Title</b>', $etext->text);
        $this->assertTrue((bool)$etext->html);

        $sutil = new NeatlineFeatures_Utils_View();
        $sutil->setEditOptions(
            "Elements[{$this->_subject->id}][0]", 'Subject', array(),
            $this->_item, $this->_subject);
        $etext = $sutil->getElementText();
        $this->assertEquals('Subject', $etext->text);
        $this->assertFalse((bool)$etext->html);
    }

    /**
     * This tests the isHtml predicate in a POST request, when it is true.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsHtmlInPostTrue()
    {
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array('html' => '1')
        );
        $this->assertTrue($this->_cutil->isHtml());
    }

    /**
     * This tests the isHtml predicate in a POST request, when it is false.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsHtmlInPostFalse()
    {
        $_POST['Elements'][(string)$this->_cutil->getElementId()] = array(
            '0' => array()
        );
        $this->assertFalse($this->_cutil->isHtml());
    }

    /**
     * This tests the isHtml predicate outside of a POST request, when it is 
     * true.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsHtmlNoPostTrue()
    {
        $tutil = new NeatlineFeatures_Utils_View();
        $tutil->setEditOptions(
            "Elements[{$this->_title->id}][0]", '<b>A Title</b>', array(),
            $this->_item, $this->_title);
        $this->assertTrue($tutil->isHtml());
    }

    /**
     * This tests the isHtml predicate outside of a POST request, when it is 
     * false.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testIsHtmlNoPostFalse()
    {
        $sutil = new NeatlineFeatures_Utils_View();
        $sutil->setEditOptions(
            "Elements[{$this->_subject->id}][0]", 'Subject', array(),
            $this->_item, $this->_subject);
        $this->assertFalse($sutil->isHtml());
    }

    /**
     * This tests the getUserHtml method, which returns the HTML string for the 
     * "Use HTML" control.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetUseHtmlChecked()
    {
        $tid = $this->_title->id;
        $expected = new DOMDocument;
        $expected->loadXML(
            '<label class="use-html">Use HTML' .
            "<input type='hidden' value='1' name='Elements[{$tid}][0][html]' />" .
            "<input name='Elements[{$tid}][0][html]' id='Elements-{$tid}-0-html' " .
            'type="checkbox" value="1" checked="checked" />' .
            '</label>'
        );

        $tutil = new NeatlineFeatures_Utils_View();
        $tutil->setEditOptions(
            "Elements[{$this->_title->id}][0]", '<b>A Title</b>', array(),
            $this->_item, $this->_title);
        $actual = new DOMDocument;
        $actual->loadHTML($tutil->getUseHtml());
        $label = $actual->getElementsByTagName('label');

        $this->assertEqualXMLStructure(
            $expected->firstChild, $label->item(0), TRUE
        );
    }

    /**
     * This tests the getUserHtml method, which returns the HTML string for the 
     * "Use HTML" control.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function testGetUseHtmlUnchecked()
    {
        $sid = $this->_subject->id;
        $expected = new DOMDocument;
        $expected->loadXML(
            '<label class="use-html">Use HTML' .
            "<input type='hidden' value='1' name='Elements[{$sid}][0][html]' />" .
            "<input name='Elements[{$sid}][0][html]' id='Elements-{$sid}-0-html' " .
            'type="checkbox" value="1" />' .
            '</label>'
        );

        $sutil = new NeatlineFeatures_Utils_View();
        $sutil->setEditOptions(
            "Elements[{$this->_subject->id}][0]", 'Subject', array(),
            $this->_item, $this->_subject);
        $actual = new DOMDocument;
        $actual->loadHTML($sutil->getUseHtml());
        $label = $actual->getElementsByTagName('label');

        $this->assertEqualXMLStructure(
            $expected->firstChild, $label->item(0), TRUE
        );
    }

    /**
     * This gets the first element child of a node.
     *
     * @param DOMNode $node This is the parent node.
     *
     * @return DOMNode
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    protected function getFirstElementChild($node)
    {
        $child = $node->firstChild;

        while ($child !== NULL && $child->nodeType !== XML_ELEMENT_NODE) {
            $child = $child->nextSibling;
        }

        return $child;
    }
}

