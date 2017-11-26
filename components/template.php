<div>
    <table class="form-table">
		<tr class="wpuf-schedule-entries">
			<th><?php _e( 'Restrict form' ); ?></th>
			<td>
				<label>
					<input type="checkbox" value="true" v-model="restrict_mood">
	                <?php _e( 'Restrict form to certain users', 'weforms' ); ?>
	            </label>
        	</td>

        </tr>
        <tr class="wpuf-form-permissions" v-if="restrict_mood">
            <th>
                <?php _e( 'Allowed Users', 'weforms' ) ?>
            </th>
            <td>
                 <div style="width: 90%">
			        <multiselect :internal-search="true" :loading="isSearching" :multiple="true" :options="wpUsers" :searchable="true" @search-change="asyncSearchUser" label="name" placeholder="Type to search" track-by="id" v-model="selected">
			            <span slot="noResult">
			                No user found
			            </span>
			        </multiselect>
			    </div>
                <p class="description">
                    Only selected users will able to see and submit the form.
                </p>
            </td>
        </tr>
    </table>
</div>