<?php declare(strict_types=1);

namespace Tests\Kirameki\Dumper;

use DateTime;
use Kirameki\Dumper\Config;
use Kirameki\Dumper\Dumper;
use Kirameki\Dumper\Writer;
use Tests\Kirameki\Dumper\Samples\CircularClass;
use Tests\Kirameki\Dumper\Samples\ContextualException;
use Tests\Kirameki\Dumper\Samples\DebuggableClass;
use Tests\Kirameki\Dumper\Samples\SimpleBackedEnum;
use Tests\Kirameki\Dumper\Samples\SimpleClass;
use Tests\Kirameki\Dumper\Samples\SimpleEnum;
use function assert;
use function fclose;
use function fopen;
use function fwrite;
use function is_resource;
use function tmpfile;
use const INF;
use const NAN;
use const STDIN;

class DumpTest extends TestCase
{
    public function testSomething(): void
    {
        $vd = new Dumper();

//        dump("\nasdf\nasdf\\nabd\n");
//        $vd->dump("\nasdf\nasdf\\nabd\n\n");

        $vd->dump(["\nasdf\nasdf\\nabd\n\n"]);
        $vd->dump(["atest"]);
        $vd->dump(["atest\n"]);
        //exit(0);

        $circular = new CircularClass();
        $circular->next = new CircularClass();
        $circular->next->next = $circular;

        $ref = 'my ref';

        $closedResource = tmpfile();
        assert($closedResource !== false);
        fclose($closedResource);

        $vars = [
            null,
            -1,
            -0.0,
            1,
            1.1,
            true,
            false,
            NAN,
            INF,
            -INF,
            "text",
            "あいう",
            "text\r\n\t\"\x1B\0a3\v\sq\\na",
            "cut_me_cut_me_cut_me_cut_me_cut_me_cut_me_cut_me",
            STDIN,
            ['a' => 1, 'b' => 2, 3],
            $closedResource,
            new DateTime(),
            new ContextualException('test'),
            new SimpleClass(),
            new DebuggableClass(),
            static fn(string $str): string => 'abc' . $str,
            DateTime::createFromFormat(...),
            strstr(...),
            SimpleEnum::Option1,
            SimpleBackedEnum::Option2,
            $circular,
            &$ref,
        ];

        $vd = new Dumper();
        $vd->dump($vars);

        $vd->dump("a\u{200E}\u{200A}\u{061C}\u{0012}\u{204A}b");

        $resource = fopen('./test.html', 'w+');
        assert(is_resource($resource));
        fwrite($resource, '<html lang="ja"><meta charset="UTF-8"><body>');

        $code = 15;
        $r = (($code - 16) / 36) * 51;
        $g = ((($code - 16) % 36) / 6) * 51;
        $b = (($code - 16) % 6) * 51;

        $vd = new Dumper(
            config: new Config(
                decorator: 'html',
                writer: new Writer($resource),
            ),
        );
        $vd->dump($vars);

        fwrite($resource, '</body></html>');
    }
}
