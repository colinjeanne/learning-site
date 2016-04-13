import { childPropType } from './../propTypes';
import React from 'react';
import SkillCalendar from './skillCalendar';
import SkillFilter from './skillFilter';

const areSkillEqual = (skillA, skillB) =>
    skillA.every((part, index) => skillB[index] === part);

const childSkillUpdater = child => (oldSkill, newSkill) => {
    return Object.assign(
        {},
        child,
        {
            skills: [
                ...(child.skills.filter(
                    skill => !areSkillEqual(oldSkill, skill))),
                newSkill
            ]
        })};

const filterSkills = (skills, filter) =>
    !filter ?
        skills :
        skills.filter(skill => skill[0] === filter);

const page = props => {
    const childrenListData = props.children.map(child => {
        const handleChildSkillChange = childSkillUpdater(child);
        const onSkillChange = (oldSkill, newSkill) =>
            props.onSkillChange(handleChildSkillChange(oldSkill, newSkill));

        return (
            <section
                key={child.links.self}>
                <h1>
                    {child.name}
                </h1>
                <SkillCalendar
                    onSkillChange={onSkillChange}
                    skills={filterSkills(child.skills, props.skillFilter)} />
            </section>
        )});
    
    const noChildrenMessage =
        'You have no children currently, add some!';

    return (
        <section>
            <SkillFilter
                onSkillFiltered={props.onSkillFiltered} />
            {props.children.length ? childrenListData : noChildrenMessage}
        </section>
    )};

page.propTypes = {
    children: React.PropTypes.arrayOf(
        React.PropTypes.shape(childPropType)),
    onSkillChange: React.PropTypes.func.isRequired,
    onSkillFiltered: React.PropTypes.func.isRequired,
    skillFilter: React.PropTypes.number.isRequired
};

export default page;