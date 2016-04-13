import React from 'react';
import { skillNames } from './skillMethods';

class SkillFilter extends React.Component {
    handleSkillSelect() {
        this.props.onSkillFiltered(parseInt(this.selectElem.value));
    }
    
    render() {
        const unfilteredOption = (
            <option
                key=''
                value='0'>No Filter</option>
        );
        
        const skillOptions = [
            unfilteredOption,
            ...skillNames().map((name, index) => (
                <option
                    key={index + 1}
                    value={index + 1}>{name}</option>
            ))
        ];
        
        return (
            <section className="skillFilter">
                <label>
                    Filter Skills:
                    <select
                        onChange={() => this.handleSkillSelect()}
                        ref={elem => this.selectElem = elem}>
                        {skillOptions}
                    </select>
                </label>
            </section>
        );
    }
};

SkillFilter.propTypes = {
    onSkillFiltered: React.PropTypes.func.isRequired
};

export default SkillFilter;