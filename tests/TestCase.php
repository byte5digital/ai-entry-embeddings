<?php

namespace Byte5\AiEntryEmbeddings\Tests;

use Byte5\AiEntryEmbeddings\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
