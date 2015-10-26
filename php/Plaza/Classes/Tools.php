<?php

	namespace Bol\Plaza\Classes;

	class Tools {

			/**
			*	Converts a DOMObject into an associative array... which is nice.
			*/
		public static function xmlToArray($root) {
			$result = array();

			if ($root->hasAttributes()) {
				$attrs = $root->attributes;
				foreach ($attrs as $attr) {
					$result['@attributes'][$attr->name] = $attr->value;
				}
			}

			if ($root->hasChildNodes()) {
				$children = $root->childNodes;
				if ($children->length == 1) {
					$child = $children->item(0);
					if ($child->nodeType == XML_TEXT_NODE) {
						$result['_value'] = $child->nodeValue;
						return count($result) == 1
							? $result['_value']
							: $result;
					}
				}
				$groups = array();
				foreach ($children as $child) {
					if (!isset($result[$child->nodeName])) {
						$result[$child->nodeName] = self::xmlToArray($child);
					} else {
						if (!isset($groups[$child->nodeName])) {
							$result[$child->nodeName] = array($result[$child->nodeName]);
							$groups[$child->nodeName] = 1;
						}
						$result[$child->nodeName][] = self::xmlToArray($child);
					}
				}
			}

			return $result;
		}

		public static function debug($data, $isXml = false) {
			$trace  = debug_backtrace();
			$caller = array_shift($trace);
			$caller = array_shift($trace);
			if (isset($caller['class'])) {
				echo "Called by {$caller['class']}->{$caller['function']}()";
			} else {
				echo "Called by {$caller['function']}";
			}

			if ($isXml) {
				$dom = new \DOMDocument;
				$dom->preserveWhiteSpace = false;
				$dom->loadXML(trim($data));
				$dom->formatOutput = true;
				echo nl2br($dom->saveXml());
			} else {
				echo "<br>\n";
				echo nl2br(htmlentities($data));
				echo "\n\n<br/><br/>";
			}
		}

		public static function getGMTDateTime() {
				$date = new \DateTime();
				$date->setTimezone(new \DateTimeZone('Etc/Greenwich'));
				return $date->format('Y-m-d\TH:i:s');
		}

		public static function arrayToXML(&$a, $level = 0) {
			$xml = '';
			foreach ($a as $nodeName => $nodeValue) {
				if (is_array($nodeValue) && !isset($nodeValue[0])) {
						// Associative, let's go deeper.........inception....
					$xml .= str_repeat("\t", $level);
					$xml .= '<'.$nodeName.">\n";
					$xml .= self::arrayToXML($nodeValue, $level+1);
					$xml .= str_repeat("\t", $level);
					$xml .= '</'.$nodeName.">\n";
				} elseif (is_array($nodeValue) && isset($nodeValue[0])) {
						// Probably numeric.
					$indentation = str_repeat("\t", $level);
					foreach ($nodeValue as $item) {
						$xml .= $indentation . '<'.$nodeName.'>' . $item . '</'.$nodeName.">\n";
					}
				} else {
					$xml .= str_repeat("\t", $level);
					$xml .= '<'.$nodeName.">";
					$xml .= htmlentities($nodeValue); // @TODO: This is WEAK as it hasn't been tested and probably isn't very fool proof.
					$xml .= '</'.$nodeName.">\n";
				}
			}

			return $xml;
		}

		public static function replaceEmptyArraysWithEmptyStrings($arr) {
			if (is_array($arr))
				foreach ($arr as $key => $val)
					$arr[$key]=is_array($val) ? '' : (string)$val;
			return $arr;
		}
	}
?>