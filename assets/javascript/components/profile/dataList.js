import React from 'react';

const dataList = props => {
    const listItems = props.items.map(item =>
        <li key={item.key}>
            {item.text}
        </li>
    );
    
    const listNode = (
        <ul>
            {listItems}
        </ul>
    );
    
    const emptyMessageNode = (
        <div>{props.emptyMessage}</div>
    );
    
    return listItems.length ? listNode : emptyMessageNode;
};

dataList.propTypes = {
    emptyMessage: React.PropTypes.string.isRequired,
    items: React.PropTypes.arrayOf(
        React.PropTypes.shape({
            text: React.PropTypes.string.isRequired,
            key: React.PropTypes.string.isRequired
        })).isRequired
}

export default dataList;