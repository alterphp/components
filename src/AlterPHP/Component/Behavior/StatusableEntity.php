<?php

namespace AlterPHP\Component\Behavior;

use Doctrine\ORM\Mapping as ORM;

trait StatusableEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=31)
     */
    protected $status;

    protected static $statusValues = null;

    /**
     * Returns entity translation prefix.
     *
     * @return string
     */
    protected static function getLowerCaseClassName()
    {
        $refClass = new \ReflectionClass(get_called_class());
        $className = $refClass->getShortName();

        return strtolower($className);
    }

    /**
     * Returns status list, with or without labels.
     *
     * @param bool  $withLabelsAsIndexes
     * @param array $filterStatus
     *
     * @return array
     */
    public static function getStatusList($withLabelsAsIndexes = false, array $filterStatus = null)
    {
        // Build $statusValues if this is the first call
        if (null === static::$statusValues) {
            static::$statusValues = array();
            $refClass = new \ReflectionClass(get_called_class());
            $classConstants = $refClass->getConstants();
            $className = $refClass->getShortName();

            $constantPrefix = 'STATUS_';
            foreach ($classConstants as $key => $val) {
                if (substr($key, 0, strlen($constantPrefix)) === $constantPrefix) {
                    static::$statusValues[$val] = static::getLowerCaseClassName().'.status.'.$val;
                }
            }
        }

        $statusValues = static::$statusValues;

        // Filter on specified status list
        if (isset($filterStatus)) {
            $statusValues = array_filter($statusValues, function ($key) use ($filterStatus) {
                return in_array($key, $filterStatus);
            }, ARRAY_FILTER_USE_KEY);
        }

        if ($withLabelsAsIndexes) {
            return array_flip($statusValues);
        }

        return array_keys($statusValues);
    }

    /**
     * Checks if status is between a "from" and a "to" status.
     *
     * @param string $from
     * @param string $to
     * @param bool   $strict
     * @param bool   $strictTo
     *
     * @return bool
     */
    public function isStatusBetween($from = null, $to = null, $strict = false, $strictTo = null)
    {
        return in_array($this->status, static::getStatusBetween($from, $to, $strict, $strictTo));
    }

    /**
     * Returns list of status between a "from" and a "to" status.
     *
     * @param string $from
     * @param string $to
     * @param bool   $strict   Is lower bound strict (or both bounds if $strictTo is null) ?
     * @param bool   $strictTo Is higher bound strict ?
     *
     * @return array
     */
    public static function getStatusBetween($from = null, $to = null, $strict = false, $strictTo = null)
    {
        $statusList = static::getStatusList();

        $strictFrom = $strict;
        $strictTo = isset($strictTo) ? (bool) $strictTo : $strict;

        // Remove status before given $from
        if (isset($from)) {
            static::checkAllowedStatus($from);
            foreach ($statusList as $key => $status) {
                if ($from !== $status) {
                    unset($statusList[$key]);
                } else {
                    if ($strictFrom) {
                        unset($statusList[$key]);
                    }
                    break;
                }
            }
        }

        // Remove status after given $to
        if (isset($to)) {
            static::checkAllowedStatus($to);
            // On inverse l'ordre des statuts
            $statusList = array_reverse($statusList);
            foreach ($statusList as $key => $status) {
                if ($to !== $status) {
                    unset($statusList[$key]);
                } else {
                    if ($strictTo) {
                        unset($statusList[$key]);
                    }
                    break;
                }
            }
            // On inverse l'ordre des statuts
            $statusList = array_reverse($statusList);
        }

        return array_values($statusList);
    }

    /**
     * Chacks that a value is a valid status.
     *
     * @param string $status
     *
     * @throws \InvalidArgumentException
     */
    protected static function checkAllowedStatus($status)
    {
        if (!in_array($status, static::getStatusList())) {
            throw new \InvalidArgumentException(
                sprintf('%s is not a valid value for $status property !', $status)
            );
        }
    }

    /**
     * Compare first status to second passed status.
     *
     * @param string $status1
     * @param string $status2
     * @param bool   $strict
     *
     * @return bool
     */
    public static function isGreaterThan($status1, $status2, $strict = true)
    {
        static::checkAllowedStatus($status1);
        static::checkAllowedStatus($status2);

        $entityStatusIdx = array_search($status1, static::getStatusList());
        $comparedStatusIdx = array_search($status2, static::getStatusList());

        if ($strict) {
            return $entityStatusIdx > $comparedStatusIdx;
        }

        return $entityStatusIdx >= $comparedStatusIdx;
    }

    /**
     * Compare entity status to passed status.
     *
     * @param mixed $status
     * @param bool  $strict
     *
     * @return bool
     */
    public function isStatusGreaterThan($status, $strict = true)
    {
        return static::isGreaterThan($this->getStatus(), $status, $strict);
    }

    /**
     * Compare first status to second passed status.
     *
     * @param string $status1
     * @param string $status2
     * @param bool   $strict
     *
     * @return bool
     */
    public static function isLowerThan($status1, $status2, $strict = true)
    {
        static::checkAllowedStatus($status1);
        static::checkAllowedStatus($status2);

        $entityStatusIdx = array_search($status1, static::getStatusList());
        $comparedStatusIdx = array_search($status2, static::getStatusList());

        if ($strict) {
            return $entityStatusIdx < $comparedStatusIdx;
        }

        return $entityStatusIdx <= $comparedStatusIdx;
    }

    /**
     * Compare entity status to passed status.
     *
     * @param mixed $status
     * @param bool  $strict
     *
     * @return bool
     */
    public function isStatusLowerThan($status, $strict = true)
    {
        return static::isLowerThan($this->getStatus(), $status, $strict);
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        static::checkAllowedStatus($status);

        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get statusLabel.
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $statusList = array_flip($this->getStatusList(true));

        return isset($statusList[$this->status]) ? $statusList[$this->status] : $this->status;
    }
}
