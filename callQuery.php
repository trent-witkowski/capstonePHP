<?php
function callQuery($pdo, $query, $error) {

  try {
    return $pdo->query($query);
  } catch(PDOException $ex) {
    $error .= $ex->getMessage();  // TODO: remove getMessage() method in production
    throw $ex;
  }

}