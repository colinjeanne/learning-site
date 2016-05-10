import Constants from '../constants/constants';
import { createAction } from 'redux-actions';

export const filterActivities = createAction(Constants.ACTIVITY_FILTERED);
export const selectActivity = createAction(Constants.SELECT_ACTIVITY);
export const showPage = createAction(Constants.SHOW_PAGE);