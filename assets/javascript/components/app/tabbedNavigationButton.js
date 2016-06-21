import React from 'react';

const tabbedNavigationButton = props => (
    <li className="tabbedNavigationButton">
        <input
            checked={!!props.selected}
            id={props.id}
            name="tab"
            onChange={() => props.onSelect(props.id)}
            type="radio" />
        <label htmlFor={props.id}>
            <span className="narrowTitle">{props.title.narrow}</span>
            <span className="wideTitle">{props.title.wide}</span>
        </label>
    </li>
);

tabbedNavigationButton.propTypes = {
    id: React.PropTypes.string.isRequired,
    onSelect: React.PropTypes.func.isRequired,
    selected: React.PropTypes.bool,
    title: React.PropTypes.shape({
        narrow: React.PropTypes.string.isRequired,
        wide: React.PropTypes.string.isRequired
    }).isRequired
};

export default tabbedNavigationButton;