<?php
/**
 * Hotelsnl_Sniffs_ControlStructures_InlineControlStructureSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Hotelsnl Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Hotelsnl_Sniffs_ControlStructures_InlineIfDeclarationSniff.
 *
 * Tests the spacing of shorthand IF statements.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Hotelsnl Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.3
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Hotelsnl_Sniffs_ControlStructures_InlineIfDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_INLINE_THEN);

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find the opening bracket of the inline IF.
        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($tokens[$i]['parenthesis_opener']) === true
                && $tokens[$i]['parenthesis_opener'] < $i
            ) {
                $i = $tokens[$i]['parenthesis_opener'];
                continue;
            }

            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                break;
            }
        }

        if ($i <= 0) {
            // Could not find the begining of the statement. Probably not
            // wrapped with brackets, so assume it ends with a semicolon.
            $statementEnd = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
        } else {
            $statementEnd = $tokens[$i]['parenthesis_closer'];
        }

        // Make sure there are spaces around the question mark.
        $contentBefore = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        $contentAfter  = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        $spaceBefore = ($tokens[$stackPtr]['column'] - ($tokens[$contentBefore]['column'] + strlen($tokens[$contentBefore]['content'])));
        if ($spaceBefore !== 1 && $tokens[$contentBefore]['line'] === $tokens[$stackPtr]['line']) {
            $error = 'Inline shorthand IF statement requires 1 space before THEN; %s found';
            $data  = array($spaceBefore);
            $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeThen', $data);
        }

        // Requires space OR colon after the question mark.
        $spaceAfter = (($tokens[$contentAfter]['column']) - ($tokens[$stackPtr]['column'] + 1));
        // Changed: allow ?:
        if ($spaceAfter !== 1 && $tokens[$stackPtr]['type'] !== 'T_INLINE_THEN') {
            $error = 'Inline shorthand IF statement requires 1 space after THEN; %s found';
            $data  = array($spaceAfter);
            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterThen', $data);
        }

        // If there is an else in this condition, make sure it has correct spacing.
        $inlineElse = $phpcsFile->findNext(T_COLON, ($stackPtr + 1), $statementEnd, false);
        $isInlineIfElse = $tokens[$inlineElse]['column'] - $tokens[$stackPtr]['column'] === 1;
        if ($inlineElse === false) {
            // No else condition.
            return;
        }

        $contentBefore = $phpcsFile->findPrevious(T_WHITESPACE, ($inlineElse - 1), null, true);
        $contentAfter  = $phpcsFile->findNext(T_WHITESPACE, ($inlineElse + 1), null, true);

        $spaceBefore = ($tokens[$inlineElse]['column'] - ($tokens[$contentBefore]['column'] + strlen($tokens[$contentBefore]['content'])));
        if ($spaceBefore !== 1 && $tokens[$contentBefore]['line'] === $tokens[$inlineElse]['line'] && !$isInlineIfElse) {
            $error = 'Inline shorthand IF statement requires 1 space before ELSE; %s found';
            $data  = array($spaceBefore);
            $phpcsFile->addError($error, $inlineElse, 'SpacingBeforeElse', $data);
        }

        $spaceAfter = (($tokens[$contentAfter]['column']) - ($tokens[$inlineElse]['column'] + 1));
        $colonAfter = $tokens[$stackPtr + 1]['type'] === 'T_COLON';
        if ($spaceAfter !== 1 && !$colonAfter) {
            $error = 'Inline shorthand IF statement requires 1 space after ELSE; %s found';
            $data  = array($spaceAfter);
            $phpcsFile->addError($error, $inlineElse, 'SpacingAfterElse', $data);
        }

    }//end process()


}//end class


?>
