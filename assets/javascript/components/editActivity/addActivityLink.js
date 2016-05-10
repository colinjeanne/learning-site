import React from 'react';

class AddActivityLink extends React.Component {
    handleAdd() {
        if (this.uri.checkValidity()) {
            const title = this.title.value.trim();
            const uri = this.uri.value.trim();
            if (title && uri) {
                this.props.onAdd({
                    title,
                    uri
                });
                this.title.value = '';
                this.uri.value = '';
            }
        }
    }
    
    render() {
        return (
            <section className="addActivityLink">
                <label>
                    Title:
                    <input
                        placeholder="Link title"
                        ref={elem => this.title = elem} />
                </label>
                <label>
                    URI:
                    <input
                        placeholder="Link URI"
                        ref={elem => this.uri = elem}
                        type="url" />
                </label>
                <button
                    onClick={() => this.handleAdd()}
                    type="button">Add</button>
            </section>
        );
    }
};

AddActivityLink.propTypes = {
    onAdd: React.PropTypes.func.isRequired
};

export default AddActivityLink;