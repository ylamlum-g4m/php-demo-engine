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

namespace ABadCafe\PDE\Routine;

use ABadCafe\PDE;

/**
 * Base
 *
 * Common base class for routines
 */
abstract class Base implements PDE\IRoutine {

    /**
     * @var object $oParameters - basic key value structure
     */
    protected object $oParameters;

    /**
     * @var PDE\IDisplay $oDisplay
     */
    protected PDE\IDisplay $oDisplay;

    protected bool $bEnabled = false, $bCanRender = false;

    protected float $fUntil = 0.0;

    /**
     * Basic constructor
     *
     * @implements IRoutine::__construct()
     */
    public function __construct(PDE\IDisplay $oDisplay, array $aParameters = []) {
        $this->oParameters = (object)$this->mergeDefaultParameters();
        $this->setDisplay($oDisplay);
        $this->setParameters($aParameters);
    }

    /**
     * @inheritDoc
     * @implements IParameterisable::setParameters()
     *
     * Each input value is key checked against the DEFAULT_PARAMETERS set and if the key matches the
     * value is first type cooerced then assigned.
     */
    public function setParameters(array $aParameters) : self {
        $bChanged  = false;
        $aDefaults = $this->mergeDefaultParameters();
        foreach ($aParameters as $sParameterName => $mParameterValue) {
            if (isset($aDefaults[$sParameterName])) {
                settype($mParameterValue, gettype($aDefaults[$sParameterName]));
                if ($mParameterValue != $this->oParameters->{$sParameterName}) {
                    $this->oParameters->{$sParameterName} = $mParameterValue;
                    $bChanged = true;
                }
            }
        }
        if ($bChanged) {
            $this->parameterChange();
        }
        return $this;
    }

    /**
     * @inheritDoc
     * @implements IRoutine::enable()
     */
    public function enable(int $iFrameNumber, float $fTimeIndex) : self {
        // Enable the routine if it can be rendered.
        if ( ($this->bEnabled = $this->bCanRender) ) {
            $this->fUntil = $this->oParameters->fDuration > 0.0 ?
                $fTimeIndex + $this->oParameters->fDuration : 0.0;
        }
        return $this;
    }

    /**
     * @inheritDoc
     * @implements IRoutine::disable()
     */
    public function disable(int $iFrameNumber, float $fTimeIndex) : self {
        $this->bEnabled = false;
        return $this;
    }

    /**
     * Returns true if the effect can render right now, taking into account expected duration, etc.
     *
     * @param  int   $iFrameNumber
     * @param  float $fTimeIndex
     * @return bool
     */
    public function canRender(int $iFrameNumber, float $fTimeIndex) : bool {
        $bWasEnabled   = $this->bEnabled;
        $bStillEnabled = $bWasEnabled && (
            $this->fUntil > 0.0 ?
                ($this->fUntil > $fTimeIndex) :
                true
            );
        // Disable via the method call in case any routines override it.
        if ($bWasEnabled && !$bStillEnabled) {
            $this->disable($iFrameNumber, $fTimeIndex);
        }
        return $this->bEnabled && $this->bCanRender;
    }

    /**
     * Hook function called if any of the parameters have changed during a call to SetParameters
     */
    protected abstract function parameterChange();

    /**
     * @return mixed[] - associative key/value pair of the default parameters
     */
    private function mergeDefaultParameters() : array {
        return array_merge(self::COMMON_PARAMETERS, static::DEFAULT_PARAMETERS);
    }
}
