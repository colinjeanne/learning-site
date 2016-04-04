import App from './app';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { showPage } from '../../actions/navigation';

const mapStateToProps = state => {
    return {
        displayName: state.user.me.name,
        page: state.navigation.page
    };
};

const mapDispatchToProps = dispatch => {
    return bindActionCreators({
        onTabSelected: showPage,
        onUserMenuClick: showPage
    },
    dispatch);
};

export default connect(mapStateToProps, mapDispatchToProps)(App);