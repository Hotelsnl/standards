<?php
/**
 * Hotelsnl_Sniffs_ControlStructures_LongConditionClosingCommentSniff.
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
 * Hotelsnl_Sniffs_ControlStructures_LongConditionClosingCommentSniff.
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
class Hotelsnl_Sniffs_Commenting_LongConditionClosingCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );

    /**
     * The length that a code block must be before
     * requiring a closing comment.
     *
     * @var int
     */
    protected $lineLimit = 20;

    protected $lastFoundLine = 0;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLOSE_CURLY_BRACKET);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
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

        if (isset($tokens[$stackPtr]['scope_condition']) === false) {
            // No scope condition. It is a function closer.
            return;
        }

        $expected = '<?php';
        $comment  = $phpcsFile->findNext(array(T_COMMENT), $stackPtr, null, false);
        if (trim($tokens[$comment]['content']) !== $expected && $tokens[$comment]['level'] === 1 && $tokens[$comment]['line'] === $this->lastFoundLine && $tokens[$stackPtr]['line'] === $tokens[$comment]['line']) {
            $found = trim($tokens[$comment]['content']);
            $error = 'Do not use closing comments!';
            $data  = array(
                      $expected,
                      $found,
                     );
            $phpcsFile->addError($error, $stackPtr, 'Invalid', $data);
            return;
        }
        $this->lastFoundLine = $tokens[$comment]['line'];
    }//end process()


}//end class


?>
