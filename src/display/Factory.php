<?php
/**
 *                   ______                            __
 *           __     /\\\\\\\\_                        /\\\
 *          /\\\  /\\\//////\\\_                      \/\\\
 *        /\\\//  \///     \//\\\    ________       ___\/\\\         _______
 *      /\\\//               /\\\   /\\\\\\\\\_    /\\\\\\\\\       /\\\\\\\\_
 *    /\\\//_              /\\\\/   /\\\/////\\\   /\\\////\\\     /\\\/////\\\
 *    \////\\\ __          /\\\/    \/\\\   \/\\\  \/\\\  \/\\\    /\\\\\\\\\\\
 *        \////\\\ __      \///_     \/\\\___\/\\\  \/\\\__\/\\\   \//\\\//////_
 *            \////\\\       /\\\     \/\\\\\\\\\\   \//\\\\\\\\\    \//\\\\\\\\\
 *                \///       \///      \/\\\//////     \/////////      \/////////
 *                                      \/\\\
 *                                       \///
 *
 *                         /P(?:ointless|ortable|HP) Demo Engine/
 */

declare(strict_types=1);

namespace ABadCafe\PDE\Display;

use ABadCafe\PDE;

/**
 * Factory
 *
 * Basic singleton for constructing a PDE\IDisplay implementation for a given desrciption.
 */
class Factory {

    const TYPES = [
        'PlainASCII'        => PlainASCII::class,
        'BasicRGB'          => BasicRGB::class,
        'RGBASCII'          => RGBASCII::class,
        'ASCIIOverRGB'      => ASCIIOverRGB::class,
        'RGBASCIIOverRGB'   => RGBASCIIOverRGB::class,
        'DoubleVerticalRGB' => DoubleVerticalRGB::class,
    ];

    private static ?self $oInstance = null;

    /**
     * Singleton constructor
     */
    private function __construct() {

    }

    /**
     * Singleton accessor
     *
     * @return self
     */
    public static function get() : self {
        if (null === self::$oInstance) {
            self::$oInstance = new self;
        }
        return self::$oInstance;
    }

    /**
     * Factory method
     *
     * @param  string $sKind
     * @paran  int    $iWidth
     * @param  int    $iHeight
     * @return PDE\IDisplay
     */
    public function create(string $sKind, int $iWidth, int $iHeight) : PDE\IDisplay {
        if (!isset(self::TYPES[$sKind])) {
            throw new \OutOfBoundsException($sKind . ' is not a known IDisplay type)');
        }
        // Issue #8 - Can't dereference array and call in single step until PHP8
        $sClassName = self::TYPES[$sKind];
        return new $sClassName($iWidth, $iHeight);
    }
}
