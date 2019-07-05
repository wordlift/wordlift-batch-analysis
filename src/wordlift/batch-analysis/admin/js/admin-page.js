(function(settings) {
	const form = document.getElementById("wlba-form");
	const progressBarEl = document.getElementById("wlba-progress-bar");
	const cleanUpEl = document.getElementById("wlba-cleanup-btn");

	const limit = parseInt(settings.limit);

	let index = 0;

	const request = function(
		action,
		offset,
		limit,
		nonce,
		links,
		includeAnnotated,
		minOccurrences,
		postType,
		localOnly
	) {
		// Record the last offset, for resume operations.
		index = offset;

		return wp.ajax
			.post(action, {
				offset: offset,
				limit: limit,
				links: links,
				include_annotated: includeAnnotated,
				min_occurrences: minOccurrences,
				post_type: postType,
				local_only: localOnly,
				_ajax_nonce: nonce
			})
			.fail(function(data) {
				alert("An error occurred. Please retry.");
			})
			.done(function(data) {
				const current = (100 * (data.index + 1)) / data.count;
				progressBarEl.style.width = current + "%";
				progressBarEl.innerText = Math.round(current) + "%";

				return data;
			})
			.then(function(data) {
				if (!data.complete)
					return request(
						action,
						parseInt(data.index) + limit,
						limit,
						data.nonce,
						links,
						includeAnnotated,
						minOccurrences,
						postType,
						localOnly
					);
			});
	};

	const start = function(action, nonce) {
		const links = form.querySelector("input[name='links']:checked").value;
		const localOnly = form.querySelector("input[name='local_only']:checked").value;
		const includeAnnotated = form.querySelector(
			"input[name='include_annotated']:checked"
		).value;
		const minOccurrences = form.querySelector(
			"input[name='min_occurrences']"
		).value;
		const postType = form.querySelector("input[name='post_type']").value;

		form.style.display = "none";
		progressBarEl.parentElement.style.display = "block";

		// At start-up index is 0. Then it tracks the last index, which is useful for resume operations.
		request(
			action,
			index,
			limit,
			nonce,
			links,
			includeAnnotated,
			minOccurrences,
			postType,
			localOnly
		);
	};
	form.addEventListener("submit", function(e) {
		e.preventDefault();

		start(
			settings["batchAnalysisAction"],
			settings["batchAnalysisAction_ajax_nonce"]
		);
	});

	cleanUpEl.addEventListener("click", function(e) {
		e.preventDefault();

		start(settings["cleanUpAction"], settings["cleanUpAction_ajax_nonce"]);
	});
})(window["wlbaBatchAnalysisSettings"]);
