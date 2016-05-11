import { childPropType } from './../propTypes';
import {
    compareSkills,
    nextBenchmark,
    previousBenchmark
} from './skillMethods';
import React from 'react';
import SkillCard from './skillCard';

const skillCalendar = props => {
    const sortedSkills = [...props.skills].sort(compareSkills);
    const skillTable = sortedSkills.map(skill =>
        [
            previousBenchmark(skill),
            skill,
            nextBenchmark(skill)
        ]);

    const skillRows = skillTable.map((skillRow, index) => (
        <tr key={'skillRow' + index}>
            <td className="previousBenchmarkColumn">
                <SkillCard
                    checked="checked"
                    onSkillChange={props.onSkillChange}
                    skill={skillRow[0]} />
            </td>
            <td>
                <SkillCard
                    onSkillChange={props.onSkillChange}
                    skill={skillRow[1]} />
            </td>
            <td className="nextBenchmarkColumn">
                <SkillCard
                    disabled="disabled"
                    onSkillChange={props.onSkillChange}
                    skill={skillRow[2]} />
            </td>
        </tr>
    ));

    return (
        <table
            className="skillCalendar">
            <thead>
                <tr>
                    <th className="previousBenchmarkColumn">
                        Recently Completed
                    </th>
                    <th>In Progress</th>
                    <th className="nextBenchmarkColumn">
                        Coming Up
                    </th>
                </tr>
            </thead>
            <tbody>
                {skillRows}
            </tbody>
        </table>
    )};

skillCalendar.propTypes = {
    onSkillChange: React.PropTypes.func.isRequired,
    skills: childPropType.skills
};

export default skillCalendar;