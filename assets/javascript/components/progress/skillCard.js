import {
    ageClasses,
    ageClassNames,
    benchmarkText,
    nextBenchmark,
    skillClass,
    skillClasses,
    skillName
} from './skillMethods';
import React from 'react';

const getClass = skill => ({
    'className': skillClass(skill),
    'title': skillName(skill)
});

const getAgeClass = skill => {
    return ageClasses[skill[3] - 1];
};

class SkillCard extends React.Component {
    handleChecked() {
        const checkState = this.checkElement.checked;

        // If the box is changed to checked then the user clicked on a skill in
        // the in progress column. Otherwise they clicked on a skill in the
        // recently completed column.
        const oldSkill = checkState ?
            this.props.skill :
            nextBenchmark(this.props.skill);

        const newSkill = checkState ?
            nextBenchmark(this.props.skill) :
            this.props.skill;

        this.props.onSkillChange(oldSkill, newSkill);
    }

    render() {
        const skillClass = getClass(this.props.skill);
        const text = benchmarkText(this.props.skill);
        const isValidSkill = text !== '';

        const skillClassElements = skillClasses().map(className => {
            const fullClassList = skillClass.className === className ?
                className + ' supportedSkill' :
                className;

            return (
                <li
                    className={fullClassList}
                    key={className}
                    title={skillClass.title}></li>
            );
        });

        const ageClass = getAgeClass(this.props.skill);
        const skillAgeClass = 'skillAge ' + ageClass;

        return isValidSkill ? (
            <section
                className="skillCard">
                <ol className="skillClass">
                    {skillClassElements}
                </ol>
                <div className="skillBody">
                    <div
                        className={skillAgeClass}
                        title={ageClassNames[ageClass]}>
                    </div>
                    <input
                        checked={this.props.checked === 'checked'}
                        disabled={this.props.disabled === 'disabled'}
                        onChange={() => this.handleChecked()}
                        ref={elem => this.checkElement = elem}
                        type="checkbox" />
                    <div className="skillText">
                        {text}
                    </div>
                </div>
            </section>
        ) : null;
    }
}

SkillCard.propTypes = {
    checked: React.PropTypes.string,
    disabled: React.PropTypes.string,
    onSkillChange: React.PropTypes.func.isRequired,
    skill: React.PropTypes.arrayOf(React.PropTypes.number).isRequired
};

export default SkillCard;