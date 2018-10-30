<?php

declare(strict_types = 1);

/*
 * This file is part of the FiveLab Transactional package.
 *
 * (c) FiveLab <mail@fivelab.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FiveLab\Component\Transactional;

/**
 * Chain transactional
 *
 * @author Vitaliy Zhuk <v.zhuk@fivelab.org>
 */
class ChainTransactional extends AbstractTransactional
{
    /**
     * @var array|TransactionalInterface[]
     */
    private $layers = [];

    /**
     * Construct
     *
     * @param array|TransactionalInterface[] $layers
     */
    public function __construct(array $layers = [])
    {
        foreach ($layers as $layer) {
            $this->addTransactional($layer);
        }
    }

    /**
     * Add transactional layer
     *
     * @param TransactionalInterface $transactional
     *
     * @return ChainTransactional
     */
    public function addTransactional(TransactionalInterface $transactional)
    {
        $this->layers[spl_object_hash($transactional)] = $transactional;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function begin($key = null, array $options = []): void
    {
        foreach ($this->layers as $transactional) {
            $transactional->begin($key, $options);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function commit($key = null): void
    {
        foreach ($this->layers as $transactional) {
            $transactional->commit($key);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function rollback($key = null): void
    {
        foreach ($this->layers as $transactional) {
            $transactional->rollback($key);
        }
    }
}
