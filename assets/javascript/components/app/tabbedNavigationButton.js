import React from 'react';

const tabbedNavigationButton = props => (
    <li className="tabbedNavigationButton">
        <input
            checked={!!props.selected}
            id={props.id}
            name="tab"
            onChange={() => props.onSelect(props.id)}
            type="radio" />
        <label htmlFor={props.id}>{props.title}</label>
    </li>
);

tabbedNavigationButton.propTypes = {
    id: React.PropTypes.string.isRequired,
    onSelect: React.PropTypes.func.isRequired,
    selected: React.PropTypes.bool,
    title: React.PropTypes.string.isRequired
};

export default tabbedNavigationButton;