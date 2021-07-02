<?php
/*
 * Copyright (c) 2013 by Wolfgang Wiedermann
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; version 3 of the
 * License.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 */

class BackupController {

private $dispatcher, $mandant_id;

# Einsprungpunkt, hier übergibt das Framework
function invoke($action, $request, $dispatcher) {
    $this->dispatcher = $dispatcher;
    $this->mandant_id = $dispatcher->getMandantId();
	
    switch($action) {
        case "sqlbackup":
            return $this->getMysqlBackup($request);
        default:
            throw new ErrorException("Unbekannte Action");
    }
}

# Erstellt ein Datenbankbackup (Insert-Statements) von 
# den Buchungen und Konten des aktuell angemeldeten Mandanten
function getMysqlBackup($request) {
    $pdo = getPdoConnection();
    $backup_sql = $this->getKontenBackup($pdo);
    $backup_sql .= $this->getBuchungenBackup($pdo);

    $result = gzencode($backup_sql);
    return wrap_response($result, "gz");
}

# Insert-Statements für alle Buchungen des Mandanten generieren
private function getBuchungenBackup($pdo) {
    $sql = "select mandant_id, buchungsnummer, buchungstext, sollkonto, habenkonto, ";
    $sql .= "betrag, datum, bearbeiter_user_id, is_offener_posten ";
    $sql .= "from fi_buchungen ";
    $sql .= "where mandant_id = :mandant_id";

    $result = "";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array("mandant_id" => $this->mandant_id));

    while($obj = $stmt->fetchObject()) {
        $result .= "insert into fi_buchungen (mandant_id, buchungsnummer, buchungstext, sollkonto, habenkonto, ";
        $result .= "betrag, datum, bearbeiter_user_id, is_offener_posten) values ";
        $result .= "(".$obj->mandant_id.", ".$obj->buchungsnummer.", ";
        $result .= "".$pdo->quote($obj->buchungstext).", ";
        $result .= "".$pdo->quote($obj->sollkonto).", ";
        $result .= "".$pdo->quote($obj->habenkonto).", ";
        $result .= "".$obj->betrag.", '".$obj->datum."', ";
        $result .= "".$obj->bearbeiter_user_id.", ".$obj->is_offener_posten."); \n";
    }
    return $result;
}

# Insert-Statements für alle Konten des Mandanten generieren
private function getKontenBackup($pdo) {
    $sql = "select mandant_id, kontonummer, bezeichnung, kontenart_id ";
    $sql .= "from fi_konto ";
    $sql .= "where mandant_id = :mandant_id";

    $result = "";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array("mandant_id" => $this->mandant_id));

    while($obj = $stmt->fetchObject()) {
        $result .= "insert into fi_konto (mandant_id, kontonummer, bezeichnung, kontenart_id) values ";
        $result .= "(".$obj->mandant_id.", ";
        $result .= "".$pdo->quote($obj->kontonummer).", ";
        $result .= "".$pdo->quote($obj->bezeichnung).", ";
        $result .= "".$obj->kontenart_id."); \n";
    }
    return $result;
}
}
