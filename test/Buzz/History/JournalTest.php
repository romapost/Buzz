<?php

namespace Buzz\History;

use Buzz\Message;

class JournalTest extends \PHPUnit_Framework_TestCase
{
    protected $request1;
    protected $request2;
    protected $request3;

    protected $response1;
    protected $response2;
    protected $response3;

    public function setUp()
    {
        $this->request1 = new Message\Request();
        $this->request1->setContent('request1');
        $this->request2 = new Message\Request();
        $this->request2->setContent('request2');
        $this->request3 = new Message\Request();
        $this->request3->setContent('request3');

        $this->response1 = new Message\Response();
        $this->response1->setContent('response1');
        $this->response2 = new Message\Response();
        $this->response2->setContent('response2');
        $this->response3 = new Message\Response();
        $this->response3->setContent('response3');
    }

    public function testRecordEnforcesLimit()
    {
        $journal = new Journal();
        $journal->setLimit(2);

        $journal->record($this->request1, $this->response1);
        $journal->record($this->request2, $this->response2);
        $journal->record($this->request3, $this->response3);

        $this->assertEquals(count($journal), 2);
    }

    public function testGetLastReturnsTheLastEntry()
    {
        $journal = new Journal();

        $journal->record($this->request1, $this->response1);
        $journal->record($this->request2, $this->response2);

        $this->assertEquals($journal->getLast()->getRequest(), $this->request2);

        return $journal;
    }

    /**
     * @depends testGetLastReturnsTheLastEntry
     */
    public function testGetLastRequestReturnsTheLastRequest(Journal $journal)
    {
        $this->assertEquals($journal->getLastRequest(), $this->request2);
    }

    /**
     * @depends testGetLastReturnsTheLastEntry
     */
    public function testGetLastResponseReturnsTheLastResponse(Journal $journal)
    {
        $this->assertEquals($journal->getLastResponse(), $this->response2);
    }

    /**
     * @depends testGetLastReturnsTheLastEntry
     */
    public function testClearRemovesEntries(Journal $journal)
    {
        $journal->clear();
        $this->assertEquals(count($journal), 0);
    }

    /**
     * @depends testGetLastReturnsTheLastEntry
     */
    public function testForeachIteratesReversedEntries(Journal $journal)
    {
        $requests = array($this->request2, $this->request1);
        $responses = array($this->response2, $this->response1);

        foreach ($journal as $index => $entry) {
            $this->assertEquals($entry->getRequest(), $requests[$index]);
            $this->assertEquals($entry->getResponse(), $responses[$index]);
        }
    }
}
