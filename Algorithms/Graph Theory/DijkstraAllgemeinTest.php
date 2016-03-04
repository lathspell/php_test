<?php

/*
 * Graphentheorie
 *
 * In einer Höhle befinden sich vier Personen sowie eine Lampe. Da die Höhle
 * dunkel ist, können die Personen den Weg nach draußen nur mithilfe der Lampe
 * finden. Die Höhle kann nur in Zweiergruppen verlassen werden. Das heißt,
 * wenn ein Pärchen die Höhle verlässt, muss eine Person wieder zurückgehen,
 * um den anderen die Lampe zurück zu bringen. Eine weitere Bedingung sind die
 * unterschiedlichen Zeiteinheiten (1, 2, 5, 10), die die Personen benötigen,
 * um die Höhle zu durchqueren. Wie gelangen alle Personen schnellstmöglich aus
 * der Höhle? Wie würden Sie das Beispiel lösen und vor allem in welcher Zeit?
 * 
 * (PHPmagazinm, Ausgabe 3.2010)
 *
 *
 * 
 * Lösung mit Hilfe eines gerichteten Graphen und des Dijkstra Algorithmus zur
 * Ermittlung der kürzesten Pfade.
 *
 * Jeder Knoten des Graphs beschreibt einen Zustand der Modellwelt und wird
 * beschrieben als:
 *      Zustand = (I, O, L) mit
 *          I = Menge der Personen in der Höhle ("Inside")
 *          O = Menge der Personen außerhalb der Höhle ("Outside")
 *          L = Aktueller Ort der Lampe (1=Inside oder 0=Outside)
 *
 * Der Weg zwischen jedem Knoten wird also von einer bzw. zwei Personen
 * gegangen und hat daher die Kosten des langsamsten der beiden bzw. der einen
 * Person.
 *
 * Bsp:
 *  2 und 10 gehen als erstes:    v = ({1,5}, {2,10}, 0); cost = max(2,10) = 10
 *  10 bleibt und 2 kommt zurück: v = ({1,2,5}, {10}, 1); cost = 2
 *
 * Den Rückweg kann auch einer der bereits draußen warteneden übernehmen so
 * das z.B. "1" mehrmals den Rückweg beschreiten kann.
 *
 * Die Lösung ist der kürzeste Pfad (oder die Pfade) zwischen der Ausgangslage
 * ({1,2,5,10}, {}, 1) und dem Ziel ({}, {1,2,5,10}, 0). Die Summe der Kantenlängen
 * (=Kosten) der einzelnen Kanten ist die Dauer die die Personen in der
 * Aufgabe brauchen.
 */

class Hoehlenproblem_Test extends PHPUnit_Framework_TestCase {

    function testDijkstra() {
        $initial = array(
            array(1, 2, 5, 10),
            array(),
            'Inside'
        );

        list($transitions, $costs) = $this->dijkstra($initial);
        self::assertEquals(17, $costs);
        self::assertEquals(5, count($transitions));
        self::assertEquals(array(1, 2),  $transitions[0]);
        self::assertEquals(array(1),     $transitions[1]);
        self::assertEquals(array(5, 10), $transitions[2]);
        self::assertEquals(array(2),     $transitions[3]);
        self::assertEquals(array(1, 2),  $transitions[4]);
    }

    /** Sortiert die Inside/Outside Arrays des State. */
    private function normalize($state) {
        list($inside, $outside, $lamp) = $state;
        sort($inside);
        sort($outside);
        return array($inside, $outside, $lamp);
    }

    private function take($set, $n) {
        if ($n < 2) {
            $f = function($element) {
                        return array($element);
                    };
            return array_map($f, $set);
        }
        $result = array();
        foreach ($set as $k => $v) {
            unset($set[$k]);
            foreach ($this->take($set, $n - 1) as $people) {
                $result[] = array_merge(array($v), $people);
            }
        }

        return $result;
    }

    private function neighborhood($state) {
        $neighbours = array();
        list($inside, $outside, $lamp) = $state;

        $origin = ($lamp == 'Outside' ? $outside : $inside);
        foreach (array_merge($this->take($origin, 1), $this->take($origin, 2)) as $people) {
            if ($lamp == 'Outside') {
                $newInside = array_merge($inside, $people);
                $newOutside = array_diff($outside, $people);
                $newLamp = 'Inside';
            } else {
                $newInside = array_diff($inside, $people);
                $newOutside = array_merge($outside, $people);
                $newLamp = 'Outside';
            }
            $neighbours[] = array($newInside, $newOutside, $newLamp);
        }

        return $neighbours;
    }

    private function transition($from, $to) {
        $key = ($from[2] == 'Inside' ? 1 : 0);
        return array_values(array_diff($to[$key], $from[$key])); // normalized keys!
    }

    private function weight($from, $to) {
        return max($this->transition($from, $to));
    }

    private function dijkstra(array $initial) {
        $visited = array();
        /* Prioritätswarteschlange erstellen, die Priorität und Element ausgibt. */
        $queue = new SplPriorityQueue();
        $queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
        /* Startknoten einfügen */
        $queue->insert(array($initial, array()), 0);

        foreach ($queue as $element) {
            list($state, $transitions) = $element['data'];
            $priority = $element['priority'];

            /* Zustand für die 'Besucht'-Liste normalisieren. */
            $state = $this->normalize($state);

            /* Knoten wurde bereits besucht, d.h. es existiert ein kürzerer Weg zu diesem Knoten. */
            if (in_array($state, $visited)) {
                continue;
            }
            /* Knoten als besucht markieren */
            $visited[] = $state;

            /* alle Personen befinden sich außerhalb der Höhle => Lösung gefunden */
            if (count($state[0]) == 0) {
                /* Pfad und positive Priorität (d.h. Dauer) zurückgeben */
                return array($transitions, -$priority);
            }

            /* benachbarte Zustände als Lösungskandidaten in die Warteschlange einfügen */
            foreach ($this->neighborhood($state) as $successor) {
                /* Transition vom aktuellen Zustand in den Nachfolgezustand berechnen */
                $transition = array($this->transition($state, $successor));
                /* Priorität aus Dauer zum aktuellen Knoten und Dauer zum Nachfolger */
                $weight = $priority - $this->weight($state, $successor);
                /* neuen Lösungskandidaten mitsamt Gewicht einfügen */
                $queue->insert(array($successor, array_merge($transitions, $transition)), $weight);
            }
        }

        /* Es wurde kein Weg gefunden */
        return array(array(), 0);
    }
}