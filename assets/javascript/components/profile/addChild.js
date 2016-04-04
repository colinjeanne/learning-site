import React from 'react';

class AddChild extends React.Component {
    handleAdd() {
        const name = this.name.value.trim();
        if (name) {
            this.props.onAdd(name);
            this.name.value = '';
        }
    }
    
    render() {
        return (
            <section
                className="addChild">
                <label>
                    Add child:
                    <input
                        placeholder="Child's name"
                        ref={elem => this.name = elem} />
                </label>
                <button
                    onClick={() => this.handleAdd()}
                    type="button">Add</button>
            </section>
        );
    }
};

AddChild.propTypes = {
    onAdd: React.PropTypes.func.isRequired
};

export default AddChild;