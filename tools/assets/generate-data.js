'use strict';

/*global process*/
const fs = require('fs');
const path = require('path');
const developmentPath = './assets/data/development.json';

const addBenchmarkIds = benchmarks => benchmarks.map(
    (benchmark, index) => (
        {
            id: index + 1,
            benchmark: benchmark
        }));

const addIdsDeep = (field, callback) => objs => objs.map(
    (obj, index) => Object.assign(
        {},
        obj,
        {
            id: index + 1,
            [field]: callback(obj[field])
        }));

const addGoalIds = addIdsDeep('benchmarks', addBenchmarkIds);
const addSkillIds = addIdsDeep('goals', addGoalIds);
const addSectionIds = addIdsDeep('skills', addSkillIds);

const addSentinelBenchmark = benchmarks => [
    ...benchmarks,
    {
        benchmark: 'SENTINEL',
        id: benchmarks.length + 1
    }
];

const getBenchmarkIds = benchmarks => addSentinelBenchmark(benchmarks).map(
    benchmark => [benchmark.id]);

const getIdsDeep = (field, callback) => objs => objs.reduce(
    (ids, obj, index) =>
        [
            ...ids,
            ...callback(obj[field]).map(
                id => [index + 1, ...id])
        ],
    []);

const getGoalIds = getIdsDeep('benchmarks', getBenchmarkIds);
const getSkillIds = getIdsDeep('goals', getGoalIds);
const getSectionIds = getIdsDeep('skills', getSkillIds);

const readAssetData = (inputPath, callback) => fs.readFile(
    inputPath,
    (err, data) => {
        if (err) {
            callback(err);
        } else {
            const outputData = addSectionIds(JSON.parse(data));
            callback(null, outputData);
        }
    });

const generateJsData = (data, outputPath) => {
    const template = `export default ${JSON.stringify(data)};`;
    fs.mkdir(
        path.dirname(outputPath),
        () => fs.writeFile(outputPath, template));
};

const areArraysEqual = (a, b) => {
    if (a.length === b.length) {
        return a.every((element, index) => element === b[index]);
    }
    
    return false;
};

const getUniqueFromSorted = sorted => sorted.reduce(
    (unique, item) => {
        if (unique.length) {
            const last = unique[unique.length - 1];
            if ((Array.isArray(item) && areArraysEqual(last, item)) ||
                (last === item)) {
                return unique;
            }
        }
        
        return [
            ...unique,
            item
        ];
    },
    []);

const generatePHPData = (ids, outputPath, outputNamespace) => {
    const goalIds = getUniqueFromSorted(ids.map(id => id.slice(0, 3)));
    const template = `<?php namespace ${outputNamespace};

const GOAL_IDS = [${goalIds.map(id => `\n    ${JSON.stringify(id)}`)}
];

const BENCHMARK_IDS = [${ids.map(id => `\n    ${JSON.stringify(id)}`)}
];
`;
    fs.mkdir(
        path.dirname(outputPath),
        () => fs.writeFile(outputPath, template));
};

readAssetData(developmentPath, (err, data) => {
    if (err) {
        console.error(err);
        process.exit(1);
    } else {
        const jsOutputPath = './assets/javascript/data/development.js';
        const phpOutputPath = './app/Assets/Data.php';
        const phpAssetNamespace = 'App\\Assets';
        
        generateJsData(data, jsOutputPath);
        
        const ids = getSectionIds(data);
        generatePHPData(ids, phpOutputPath, phpAssetNamespace);
    }
});
