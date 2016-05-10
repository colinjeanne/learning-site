import React from 'react';

class ActivityFilter extends React.Component {
    handleFilterChange() {
        const filter = this.elem.value.trim();
        this.props.onChange(filter)
    }
    
    render() {
        return (
            <section className="activitiesFilter">
                <label>
                    <span>Filter by:</span>
                    <input
                        onChange={() => this.handleFilterChange()}
                        ref={elem => this.elem = elem} />
                </label>
            </section>
        );
    }
};

ActivityFilter.propTypes = {
    onChange: React.PropTypes.func.isRequired
};

export default ActivityFilter;