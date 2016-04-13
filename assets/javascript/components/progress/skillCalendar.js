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
            <td>
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
            <td>
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
                    <th>Recently Completed</th>
                    <th>In Progress</th>
                    <th>Coming Up</th>
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