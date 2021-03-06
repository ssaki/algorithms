<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Graphp\Algorithms\Tarjan;

class TarjanTest extends TestCase
{
    protected $graph;

    protected function setUp()
    {
        $this->graph = new Graph();
    }

    public function testTwoCycles()
    {
        // Build a graph
        for ($k = 0; $k < 6; $k++) {
            $this->graph->createVertex($k);
        }
        $vertex = $this->graph->getVertices()->getVector();
        for ($offset = 0; $offset < 6; $offset += 3) {
            for ($k = 0; $k < 3; $k++) {
                $start = $vertex[$offset + $k];
                $end = $vertex[$offset + (($k + 1) % 3)];
                $start->createEdgeTo($end);
            }
        }

        // Run the algorithm
        $algorithm = new Tarjan();

        $ret = $algorithm->getStronglyConnectedVerticesFromDirectedGraph($this->graph);
        $this->assertCount(2, $ret, 'Two cycles');
        $this->assertCount(3, $ret[0]);
        $this->assertCount(3, $ret[1]);
    }

    public function testCompleteGraph()
    {
        $card = 6;

        for ($k = 0; $k < $card; $k++) {
            $this->graph->createVertex($k);
        }
        foreach ($this->graph->getVertices()->getVector() as $src) {
            foreach ($this->graph->getVertices()->getVector() as $dst) {
                if ($src === $dst)
                    continue;
                $src->createEdgeTo($dst);
            }
        }

        // Run the algorithm
        $algorithm = new Tarjan();

        $ret = $algorithm->getStronglyConnectedVerticesFromDirectedGraph($this->graph);

        $this->assertCount(1, $ret, 'One SCC');
        $this->assertCount($card, $ret[0]);
    }

    public function testNotObviousGraph()
    {
        $a = $this->graph->createVertex('a');
        $b = $this->graph->createVertex('b');
        $c = $this->graph->createVertex('c');
        $d = $this->graph->createVertex('d');

        $a->createEdgeTo($b);
        $b->createEdgeTo($d);
        $d->createEdgeTo($a);
        $d->createEdgeTo($c);
        $b->createEdgeTo($c);

        // Run the algorithm
        $algorithm = new Tarjan();

        $ret = $algorithm->getStronglyConnectedVerticesFromDirectedGraph($this->graph);

        $this->assertCount(2, $ret);
        $this->assertCount(1, $ret[0]);
        $this->assertCount(3, $ret[1]);
    }
}
