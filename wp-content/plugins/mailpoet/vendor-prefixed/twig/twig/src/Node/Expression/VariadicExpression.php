<?php
 namespace MailPoetVendor\Twig\Node\Expression; if (!defined('ABSPATH')) exit; use MailPoetVendor\Twig\Compiler; class VariadicExpression extends \MailPoetVendor\Twig\Node\Expression\ArrayExpression { public function compile(\MailPoetVendor\Twig\Compiler $compiler) { $compiler->raw('...'); parent::compile($compiler); } } 