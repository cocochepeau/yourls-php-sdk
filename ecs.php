<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\BracesPositionFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ECSConfig): void {
    $ECSConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ]);

    // import SetList here on purpose to avoid overridden by existing Skip Option in current config
    $ECSConfig->sets([SetList::PSR_12, SetList::SYMPLIFY, SetList::COMMON, SetList::CLEAN_CODE]);
    
    $ECSConfig->skip([
        NotOperatorWithSuccessorSpaceFixer::class,
        CastSpacesFixer::class,
        BinaryOperatorSpacesFixer::class,
        UnaryOperatorSpacesFixer::class,
        FunctionDeclarationFixer::class,
        ClassAttributesSeparationFixer::class,
        MethodChainingNewlineFixer::class,
        ArrayOpenerAndCloserNewlineFixer::class,
        ArrayListItemNewlineFixer::class,
        BracesPositionFixer::class
    ]);
};
