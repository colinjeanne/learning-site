import Data from './../../data/development';

export const skillClasses = () =>
    Data.reduce(
        (allClasses, skillClass) =>
            [
                ...allClasses,
                skillClass.className
            ],
        []);

export const skillNames = () =>
    Data.reduce(
        (allNames, skillClass) =>
            [
                ...allNames,
                skillClass.name
            ],
        []);

export const skillClass = skill => Data[skill[0] - 1].className;

export const skillName = skill => Data[skill[0] - 1].name;

export const benchmarks = skill =>
    Data[skill[0] - 1].skills[skill[1] - 1].goals[skill[2] - 1].benchmarks;

export const benchmarkText = skill => {
    const skillBenchmarks = benchmarks(skill);
    if ((skill[3] - 1 < 0) || (skill[3] - 1 >= skillBenchmarks.length)) {
        return '';
    }
    
    return skillBenchmarks[skill[3] - 1].benchmark;
};

export const previousBenchmark = skill => [
    skill[0],
    skill[1],
    skill[2],
    skill[3] - 1
];

export const nextBenchmark = skill => [
    skill[0],
    skill[1],
    skill[2],
    skill[3] + 1
];

export const compareSkills = (skillA, skillB) => {
    let comp = 0;
    for (let i = 0; (i < 4) && (comp === 0); ++i) {
        if (skillA[i] < skillB[i]) {
            comp = -1;
        } else if (skillA[i] > skillB[i]) {
            comp = 1;
        }
    }
    
    return comp;
};